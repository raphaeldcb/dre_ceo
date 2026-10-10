# Deploy no HostGator - rdcb.com.br/dre_ceo/

## Passos para Publicação

### 1. Clonar/Fazer Upload dos Arquivos
```bash
# Via SSH no HostGator
ssh seu_usuario@seu_host

# Ir para public_html
cd public_html

# Clonar o repositório
git clone https://github.com/raphaeldcb/dre_ceo.git

# Ou fazer upload via FTP/SFTP
```

### 2. Criar Arquivo .env
```bash
cd dre_ceo

# Criar .env baseado em .env.example
cp .env.example .env

# Editar .env com dados do HostGator
nano .env
```

**Conteúdo do .env para HostGator:**
```
APP_NAME=DRE_CEO_Dashboard
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rdcb.com.br/dre_ceo

DB_HOST=localhost
DB_PORT=3306
DB_NAME=seu_banco_dre_ceo
DB_USER=seu_usuario_db
DB_PASSWORD=sua_senha_db
DB_CHARSET=utf8mb4

UPLOAD_DIR=public/uploads
MAX_UPLOAD_SIZE=52428800
```

### 3. Criar Banco de Dados no HostGator
- Ir em cPanel → MySQL Databases
- Criar novo banco: `seu_usuario_dre_ceo`
- Criar novo usuário e atribuir permissões total
- Usar os dados no .env

### 4. Executar Migrations
```bash
# Ainda em /dre_ceo/
php scripts/create_database.php
```

Ou criar tabelas via phpMyAdmin no cPanel:
```sql
-- Copiar todo o SQL de setup do projeto
```

### 5. Permissões de Pastas
```bash
# Dar permissão de escrita para uploads e logs
chmod 755 public/uploads
chmod 755 logs
chmod 755 cache
chmod 755 tmp
```

### 6. Configurar .htaccess
O arquivo `.htaccess` já está configurado para funcionar com:
- Caminho: `/dre_ceo/`
- URL: `https://rdcb.com.br/dre_ceo/`

### 7. Acessar a Aplicação
```
https://rdcb.com.br/dre_ceo/
```

## Troubleshooting

### Erro 404
- Verificar se mod_rewrite está ativado (cPanel → Apache Modules)
- Verificar se .htaccess tem permissão de leitura (644)

### Erro de Banco de Dados
- Verificar credenciais em .env
- Verificar se banco foi criado
- Verificar se tabelas foram criadas via migrations

### Erro de Upload
- Verificar permissões da pasta `public/uploads` (755)
- Verificar limite de upload em .env vs php.ini do HostGator

### Erro de Headers
- HostGator pode ter limite de headers
- Se problemas com sessão, verificar php.ini em cPanel

## URLs Importantes

- **App**: https://rdcb.com.br/dre_ceo/
- **Upload**: https://rdcb.com.br/dre_ceo/upload
- **Dashboard**: https://rdcb.com.br/dre_ceo/dashboard
- **API**: https://rdcb.com.br/dre_ceo/api/dashboard/*

## Segurança

1. Manter `.env` fora do git (está em .gitignore ✓)
2. Desativar APP_DEBUG em produção ✓
3. Usar HTTPS (rdcb.com.br deve ter SSL)
4. Atualizar senhas padrão do banco
5. Fazer backups regulares da pasta `dre_ceo/`

## Suporte HostGator

Se precisar ajuda:
- cPanel Chat Support
- Knowledge Base: https://support.hostgator.com/
