# DRE CEO Dashboard - Design Spec

**Data**: 2026-10-08  
**Versão**: 1.0  
**Status**: Design Phase  

---

## 1. Visão Geral

Sistema web simples para carregar dados de DRE (Demonstração de Resultado do Exercício) mensais em formato Excel padronizado, armazenar em banco de dados normalizado e visualizar com dashboards comparativos (por área e entre áreas).

**Casos de uso principal:**
- Admin carrega arquivo Treasy_DRE.xlsx, seleciona área (1 de 8 máximo)
- Sistema parse Excel → normaliza → salva em MySQL
- Gerente de área acessa dashboard: vê sua área em 3 visualizações (gráficos linha, barras, tabelas)
- Comparativo: todas as 8 áreas lado a lado para análise CEO

---

## 2. Requisitos Funcionais

### 2.1 Autenticação & Acesso
- [x] Login com email/senha (bcrypt hash)
- [x] Sessão com timeout (2 horas inatividade)
- [x] 2 papéis: `admin` (cria usuários, faz uploads) e `user` (visualiza dados)
- [x] Cada usuário vinculado a 1 área (até 8)
- [x] Usuário `user` vê apenas sua área; `admin` vê tudo
- [x] Gestão de usuários: criar, ativar/desativar (admin only)

### 2.2 Upload de Arquivo
- [x] Formulário: seleciona arquivo .xlsx + área
- [x] Validações:
  - Arquivo é .xlsx?
  - Tem aba "DRE Sintético"?
  - Estrutura esperada? (11 linhas × 12 meses)
- [x] Parser ExcelParser.php:
  - Lê Treasy_DRE.xlsx
  - Extrai 11 linhas de DRE × 12 meses
  - Normaliza 8 colunas de análise por mês
  - Converte em 132 registros `dre_valores`
- [x] Armazenamento:
  - Transação MySQL (rollback se erro)
  - Limpa dados antigos (mesmo ano/mês/área)
  - Registra histórico em `uploads` table
- [x] Feedback: "✓ 132 linhas importadas"

### 2.3 Dashboard Visualizações
**Estrutura:** Abas (8 por área + 1 Comparativo)

Cada aba de área contém:
1. **Gráfico de Linha**: Receita (Planejado vs Realizado, 12 meses)
2. **Gráfico de Linha**: EBITDA (Planejado vs Realizado, 12 meses)
3. **Gráfico de Barras**: Comparação Mensal (todas 11 linhas de DRE × 12 meses)
4. **Tabela Comparativa**: Todas 11 linhas × 12 meses + análises (Planejado, Realizado, Variação %)

Aba Comparativo:
- Gráfico de Linha: RECEITA BRUTA (8 áreas sobrepostas)
- Gráfico de Linha: EBITDA (8 áreas sobrepostas)
- (Tabelas comparativas por linha, se espaço)

### 2.4 Relatórios
- [x] Exportação para PDF (opcional, Phase 2)
- [x] Filtro por ano (2026, 2027, ...)
- [x] Historico de uploads (admin)

---

## 3. Requisitos Não-Funcionais

- **Segurança:**
  - SQL Injection: prepared statements 100%
  - XSS: htmlspecialchars() em toda saída
  - CSRF: token por sessão, validado em POST
  - Senhas: mínimo 8 caracteres, bcrypt
  - Sessão: timeout 2 horas

- **Performance:**
  - Índices MySQL em (area_id, ano, mes)
  - Carregamento lazy de gráficos (Chart.js cliente)
  - 132 registros/upload cabe em transação < 1s

- **Escalabilidade:**
  - Schema normalizado (suporta multi-ano, histórico)
  - Sem limite de uploads por área
  - 8 áreas × 12 meses × 11 linhas = 1.056 registros max em memória

- **Usabilidade:**
  - UI simples Bootstrap 5
  - Validações client + server
  - Mensagens de erro claras

---

## 4. Arquitetura

### 4.1 Stack Tecnológico
- **Servidor**: PHP 8.1+ (puro, sem framework pesado)
- **Banco**: MySQL 8.0+
- **Frontend**: Bootstrap 5 + Chart.js + Vanilla JS
- **Deploy**: Apache/Nginx + FPM (local: `php -S localhost:8000`)

### 4.2 Estrutura de Pastas
```
dre_ceo/
├── public/
│   ├── index.php          (router centralizado)
│   ├── uploads/           (arquivos temporários)
│   ├── css/style.css
│   └── js/
│       ├── chart-config.js
│       └── dashboard.js
│
├── src/
│   ├── controllers/       (AuthController, UploadController, DashboardController)
│   ├── models/            (User, Area, DreLinha, DreValor)
│   ├── services/          (AuthService, ExcelParser, DashboardService)
│   ├── middleware/        (AuthMiddleware)
│   └── utils/             (Database singleton)
│
├── views/
│   ├── login.php
│   ├── upload.php
│   ├── dashboard.php
│   ├── admin.php
│   └── components/
│       ├── header.php
│       └── footer.php
│
├── docs/
│   ├── schema.sql
│   ├── setup.md
│   └── superpowers/specs/2026-10-08-dre-ceo-design.md
│
├── .env.example
├── composer.json
└── README.md
```

### 4.3 Camadas

| Camada | Componentes | Responsabilidade |
|--------|-------------|------------------|
| **Router** | public/index.php | Mapear URL → controller, aplicar middleware |
| **Middleware** | AuthMiddleware | Validar sessão, autenticação, autorização |
| **Controllers** | Auth, Upload, Dashboard | Lógica HTTP, validação input, chamadas service |
| **Services** | ExcelParser, DashboardService, AuthService | Lógica de negócio, transformações |
| **Models** | User, Area, DreLinha, DreValor | Acesso BD, ORM simples (queries) |
| **Views** | PHP templates | Renderização HTML |
| **Database** | PDO singleton | Conexão MySQL, prepared statements |

---

## 5. Banco de Dados

### 5.1 Tabelas

**users** (autenticação)
```
id, username, email, password_hash, area_id (FK), role, is_active, 
created_at, updated_at
```

**areas** (até 8 áreas)
```
id, nome, descricao, created_at
```

**dre_linhas** (estrutura fixa: 11 linhas)
```
id, ordem, nome, tipo (receita|deducao|despesa_variavel|despesa_fixa|resultado), 
created_at
```

**dre_valores** (dados mensais normalizados)
```
id, area_id (FK), dre_linha_id (FK), mes (1-12), ano,
valor_planejado, valor_realizado,
analise_vertical_planejado, analise_vertical_realizado,
analise_horizontal_planejado, analise_horizontal_realizado,
variacao_planejado_realizado,
created_at, updated_at
```
**Unique constraint**: `(area_id, dre_linha_id, mes, ano)`

**uploads** (histórico)
```
id, area_id (FK), user_id (FK), arquivo_nome, mes, ano, 
linhas_importadas, status, mensagem_erro, created_at
```

### 5.2 Índices
- `dre_valores`: idx_area_ano_mes (area_id, ano, mes)
- `uploads`: idx_area_ano_mes (area_id, ano, mes)
- `users`: idx_area (area_id)

---

## 6. Fluxos Principais

### 6.1 Fluxo de Login
```
1. Acessa /login
2. Submete email + senha
3. AuthService.login() valida credenciais (bcrypt)
4. Session criada: user_id, username, area_id, role
5. Redireciona para /dashboard
```

### 6.2 Fluxo de Upload
```
1. Admin acessa /upload
2. Seleciona arquivo + área
3. POST /api/upload
4. Backend valida arquivo + estrutura
5. ExcelParser.parse() → 132 registros
6. MySQL: beginTransaction() → delete antigos → insert novos → commit()
7. Upload.create() registra histórico
8. JSON: { success: true, linhas_importadas: 132 }
9. Redireciona para /dashboard
```

### 6.3 Fluxo de Dashboard
```
1. User acessa /dashboard
2. AuthMiddleware.requireLogin() valida sessão
3. DashboardService.getAreaData(user_area_id) busca MySQL
4. PHP renderiza views com dados
5. Chart.js renderiza 3 gráficos (linha, barras, tabela)
6. User clica aba "Comparativo"
7. API GET /api/dashboard/comparative-data?ano=2026
8. Retorna JSON com 8 áreas × linhas prioritárias
9. Chart.js renderiza gráficos multi-série
```

---

## 7. Componentes Principais

### 7.1 ExcelParser.php
- `parse(filePath)`: retorna array [ data => [], linhas_count => 132, errors => [] ]
- Validações: aba existe? Estrutura correta?
- Conversão de tipos: strings → números
- Tratamento de None (análise horizontal jan = null)

### 7.2 AuthService.php
- `login(email, password)`: verifica bcrypt, cria session
- `logout()`: destroi session
- `createUser(...)`: hash password, insere BD, validações
- `getCSRFToken()`: gera/retorna token
- Static: `isLoggedIn()`, `isAdmin()`, `canAccessArea()`

### 7.3 DashboardService.php
- `getAreaData(areaId, ano)`: SELECT com JOIN, organiza por linha+mês
- `getComparativeData(areaIds, ano)`: SELECT para todas áreas, uma linha
- `getPriorityLines()`: retorna IDs das 6 linhas principais (RECEITA, EBITDA, etc)

### 7.4 Middleware/AuthMiddleware.php
- `requireLogin()`: redireciona se não logado
- `requireAdmin()`: 403 se não admin
- `requireAreaAccess(areaId)`: 403 se não tem acesso
- `validateSession()`: timeout 2h, redireciona /login?expired=1

---

## 8. APIs/Endpoints

| Método | Rota | Middleware | Handler | Descrição |
|--------|------|-----------|---------|-----------|
| GET | /login | - | showLogin | Formulário login |
| POST | /api/auth/login | - | handleLogin | Processa login |
| POST | /logout | requireLogin | handleLogout | Logout |
| GET | /dashboard | requireLogin | index | Dashboard |
| GET | /upload | requireAdmin | showForm | Formulário upload |
| POST | /api/upload | requireAdmin | handle | Processa upload |
| GET | /api/dashboard/area-data | requireLogin | apiAreaData | JSON área |
| GET | /api/dashboard/comparative-data | requireLogin | apiComparativeData | JSON comparativo |
| GET | /admin/users | requireAdmin | showUserManagement | Gestão usuários |
| POST | /api/admin/users/create | requireAdmin | createUser | Cria usuário |
| POST | /api/admin/users/toggle-active | requireAdmin | toggleUserActive | Ativa/desativa |

---

## 9. Validações

### 9.1 Input Validation
- **Email**: filter_var(FILTER_VALIDATE_EMAIL)
- **Senha**: mínimo 8 caracteres
- **Arquivo**: MIME type .xlsx, tamanho < 5MB
- **Area ID**: deve existir em BD
- **Mês**: 1-12
- **Ano**: inteiro válido

### 9.2 Business Logic Validation
- Arquivo tem aba "DRE Sintético"?
- Estrutura: 11 linhas × 12 meses esperadas?
- Valores numéricos convertíveis?

### 9.3 Security Validation
- CSRF token presente e válido em POST
- User tem acesso à área?
- Sessão não expirou?

---

## 10. Tratamento de Erros

| Cenário | Código | Resposta |
|---------|--------|----------|
| Login falha | 401 | JSON: { success: false, error: "..." } |
| Arquivo inválido | 400 | JSON: { success: false, error: "..." } |
| Sem permissão | 403 | HTTP 403 + mensagem |
| Não encontrado | 404 | HTTP 404 |
| Server error | 500 | JSON: { success: false, error: "..." } |
| Parse Excel falha | 400 | Rollback transação, erro detalhado |

---

## 11. Segurança - Checklist

- [x] Senhas: bcrypt (cost 10, padrão PHP)
- [x] SQL Injection: 100% prepared statements + bound parameters
- [x] XSS: htmlspecialchars() em toda saída HTML
- [x] CSRF: token gerado por sessão, obrigatório em POST
- [x] Autenticação: email + password_hash
- [x] Autorização: middleware verifica role + area_id
- [x] Sessão: timeout 2 horas, validado a cada request
- [x] Password policy: mínimo 8 caracteres
- [x] Transações: rollback se erro
- [x] Logs: uploadhistórico em `uploads` table
- [ ] HTTPS: adicionar em produção (force redirect .htaccess)
- [ ] Rate limiting: opcional Phase 2

---

## 12. Dados Iniciais

### Areas (8)
1. Marketing
2. Vendas
3. Operações
4. Financeiro
5. RH
6. TI
7. Produto
8. Administrativo

### DRE Linhas (11)
1. RECEITA DE VENDAS BRUTA
2. DEDUÇÕES DA RECEITA
3. RECEITA VENDAS LÍQUIDA
4. MARGEM DE CONTRIBUIÇÃO BRUTA
5. GASTOS E DESPESAS - VARIÁVEIS
6. MARGEM DE CONTRIBUIÇÃO
7. GASTOS E DESPESAS - FIXAS
8. EBITDA
9. DEPRECIAÇÃO
10. OUTRAS DESPESAS
11. RESULTADO OPERACIONAL

### Admin User (senha: admin123)
- username: `admin`
- email: `admin@dreceo.com`
- area_id: 1 (Marketing)
- role: `admin`

---

## 13. Roadmap (Fases)

### Phase 1: MVP (Esta spec)
- [x] Autenticação
- [x] Upload + Parser
- [x] Dashboard com gráficos
- [x] Comparativo multi-área
- [ ] (implementação)

### Phase 2 (Futuro)
- Exportar PDF
- Alertas (se realizado < planejado X%)
- Análise por período customizado
- Rate limiting
- HTTPS

### Phase 3 (Futuro)
- Multi-ano histórico gráfico
- Projeções automáticas
- Análise de tendências (ML)
- API pública para integração

---

## 14. Dependências Externas

- **PhpSpreadsheet** (composer): ler .xlsx
- **Chart.js** (CDN): gráficos cliente
- **Bootstrap 5** (CDN): styling
- MySQL 8.0+
- PHP 8.1+

---

## 15. Considerações de Deploy

- Criar .env com credenciais reais (não commitar)
- Servir com Apache/Nginx + FPM (production)
- SSL/HTTPS obrigatório (força em .htaccess)
- Backup automático do BD
- Logs em `/var/log/dre_ceo/`

---

## 16. Testes (Non-functional)

- [ ] Login: credenciais válidas/inválidas
- [ ] Upload: arquivo válido/inválido/estrutura errada
- [ ] Dashboard: carrega dados corretos
- [ ] CSRF: rejeita token inválido
- [ ] Acesso: user não vê outra área
- [ ] Timeout: sessão expira após 2h
- [ ] Gráficos: renderizam com dados reais
- [ ] Transação: rollback em erro

---

## Approval Checklist

- [x] Escopo claro (6 seções design)
- [x] Requisitos funcionais completos
- [x] Requisitos não-funcionais (seg, perf)
- [x] Arquitetura definida (MVC, stack)
- [x] Schema BD normalizado
- [x] Fluxos principais descritos
- [x] APIs especificadas
- [x] Segurança checklist
- [x] Roadmap futuro
- [x] Sem TBD, sem ambiguidades

**Status**: ✅ Pronto para Implementação
