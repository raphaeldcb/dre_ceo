-- DRE CEO Dashboard - Database Schema
-- MySQL 8.0+, UTF-8MB4 Collation
-- This file creates the complete database structure with seed data

-- Drop existing database if it exists (for fresh installation)
DROP DATABASE IF EXISTS dre_ceo_dev;

-- Create database with UTF-8MB4 support
CREATE DATABASE dre_ceo_dev
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Use the database
USE dre_ceo_dev;

-- ============================================================================
-- Table: areas
-- Purpose: Financial areas of the organization (e.g., departments, divisions)
-- 8 seed rows as per specification
-- ============================================================================
CREATE TABLE areas (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Unique area identifier (1-8)',
    nome VARCHAR(100) NOT NULL UNIQUE COMMENT 'Area name',
    descricao TEXT DEFAULT NULL COMMENT 'Area description',
    ativa BOOLEAN DEFAULT TRUE COMMENT 'Is area active?',
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation timestamp',
    atualizada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Organization financial areas/departments (max 8 per spec)';

-- Seed areas (8 rows)
INSERT INTO areas (id, nome, descricao, ativa) VALUES
(1, 'Administração', 'Área de Administração Geral', TRUE),
(2, 'Vendas', 'Departamento de Vendas e Comercial', TRUE),
(3, 'Marketing', 'Departamento de Marketing e Comunicação', TRUE),
(4, 'Recursos Humanos', 'Gestão de Pessoas e RH', TRUE),
(5, 'Operações', 'Departamento de Operações e Processos', TRUE),
(6, 'Financeiro', 'Gestão Financeira e Tesouraria', TRUE),
(7, 'Tecnologia', 'Tecnologia da Informação e Sistemas', TRUE),
(8, 'Qualidade', 'Garantia de Qualidade e Compliance', TRUE);

-- ============================================================================
-- Table: dre_linhas
-- Purpose: DRE (Income Statement) line items - 11 fixed lines per specification
-- Stores the structure of the financial statement (revenue, costs, expenses, etc.)
-- ============================================================================
CREATE TABLE dre_linhas (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Line identifier (1-11)',
    ordem TINYINT UNSIGNED NOT NULL UNIQUE COMMENT 'Display order (1-11)',
    nome VARCHAR(150) NOT NULL UNIQUE COMMENT 'Line item name',
    tipo ENUM('RECEITA', 'CUSTO', 'DESPESA', 'IMPOSTO', 'RESULTADO') NOT NULL
        COMMENT 'Line type (categorization)',
    descricao TEXT DEFAULT NULL COMMENT 'Detailed description',
    ativa BOOLEAN DEFAULT TRUE COMMENT 'Is line active?',
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='DRE line items - 11 fixed rows per specification';

-- Seed DRE lines (11 rows - standard income statement structure)
INSERT INTO dre_linhas (ordem, nome, tipo, descricao, ativa) VALUES
(1, 'Receita Bruta de Vendas', 'RECEITA', 'Total de vendas antes de devoluções e descontos', TRUE),
(2, 'Devoluções e Descontos', 'RECEITA', 'Devoluções de produtos e descontos comerciais', TRUE),
(3, 'Receita Líquida de Vendas', 'RECEITA', 'Receita bruta menos devoluções e descontos', TRUE),
(4, 'Custo dos Produtos Vendidos (CPV)', 'CUSTO', 'Custos diretos para produção dos produtos vendidos', TRUE),
(5, 'Lucro Bruto', 'RESULTADO', 'Receita líquida menos CPV', TRUE),
(6, 'Despesas Operacionais', 'DESPESA', 'Despesas gerais de operação (salários, aluguel, etc)', TRUE),
(7, 'Despesas de Vendas', 'DESPESA', 'Comissões de vendas, publicidade, marketing', TRUE),
(8, 'EBITDA', 'RESULTADO', 'Lucro bruto menos despesas operacionais', TRUE),
(9, 'Depreciação e Amortização', 'DESPESA', 'Depreciação de ativos fixos e amortização intangível', TRUE),
(10, 'Resultado Operacional (EBIT)', 'RESULTADO', 'EBITDA menos depreciação', TRUE),
(11, 'Resultado Líquido', 'RESULTADO', 'Resultado após impostos e outras receitas/despesas', TRUE);

-- ============================================================================
-- Table: users
-- Purpose: System users with authentication credentials
-- Admin user seeded with bcrypt hash of 'admin123'
-- ============================================================================
CREATE TABLE users (
    id MEDIUMINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'User email - unique',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Bcrypt password hash (cost=10)',
    area_id TINYINT UNSIGNED NOT NULL COMMENT 'User primary area assignment',
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user' COMMENT 'User role',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Is user account active?',
    ultimo_login DATETIME DEFAULT NULL COMMENT 'Last login timestamp',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_area_id
        FOREIGN KEY (area_id) REFERENCES areas(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_email_active (email, is_active),
    INDEX idx_area_id (area_id),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='System users with authentication (bcrypt passwords only)';

-- Seed admin user
-- Password: admin123 → bcrypt hash (cost=10)
-- Hash generated: $2y$10$DGDvWELhXW5pYPSuH3n3muU0L1YMwWqZ0Q1K5.YgV0P5VKKzNt6Cy
INSERT INTO users (email, password_hash, area_id, role, is_active) VALUES
('admin@dreceo.com', '$2y$10$DGDvWELhXW5pYPSuH3n3muU0L1YMwWqZ0Q1K5.YgV0P5VKKzNt6Cy', 6, 'admin', TRUE);

-- ============================================================================
-- Table: dre_valores
-- Purpose: Monthly DRE financial values for each area
-- Stores both planned (orçado) and realized (realizado) values
-- Unique constraint on (area_id, dre_linha_id, mes, ano) to prevent duplicates
-- ============================================================================
CREATE TABLE dre_valores (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    area_id TINYINT UNSIGNED NOT NULL COMMENT 'Reference to areas',
    dre_linha_id TINYINT UNSIGNED NOT NULL COMMENT 'Reference to dre_linhas',
    mes TINYINT UNSIGNED NOT NULL CHECK (mes >= 1 AND mes <= 12) COMMENT 'Month (1-12)',
    ano SMALLINT UNSIGNED NOT NULL COMMENT 'Year',
    valor_planejado DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Planned/budgeted value',
    valor_realizado DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Actual/realized value',
    variancia DECIMAL(15,2) GENERATED ALWAYS AS (valor_realizado - valor_planejado) STORED
        COMMENT 'Calculated variance (realizado - planejado)',
    percentual_realizacao DECIMAL(5,2) GENERATED ALWAYS AS
        (CASE WHEN valor_planejado = 0 THEN 0 ELSE (valor_realizado / valor_planejado * 100) END) STORED
        COMMENT 'Calculated realization percentage',
    analises TEXT DEFAULT NULL COMMENT 'Analysis notes and observations',
    status ENUM('PLANEJADO', 'PARCIAL', 'FINALIZADO') DEFAULT 'PLANEJADO' COMMENT 'Entry status',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_dre_valores_area_id
        FOREIGN KEY (area_id) REFERENCES areas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_dre_valores_linha_id
        FOREIGN KEY (dre_linha_id) REFERENCES dre_linhas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT uc_dre_valores_unique
        UNIQUE KEY (area_id, dre_linha_id, mes, ano),

    INDEX idx_area_ano_mes (area_id, ano, mes),
    INDEX idx_linha_ano_mes (dre_linha_id, ano, mes),
    INDEX idx_ano_mes (ano, mes),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Monthly DRE values (planned vs realized) per area';

-- ============================================================================
-- Table: uploads
-- Purpose: Track uploaded DRE files (typically XLSX)
-- Records file upload history, line count imported, and any error messages
-- ============================================================================
CREATE TABLE uploads (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    area_id TINYINT UNSIGNED NOT NULL COMMENT 'Area that uploaded the file',
    user_id MEDIUMINT UNSIGNED NOT NULL COMMENT 'User who performed the upload',
    arquivo_nome VARCHAR(255) NOT NULL COMMENT 'Original filename',
    arquivo_hash VARCHAR(64) DEFAULT NULL COMMENT 'SHA-256 hash for duplicate detection',
    mes TINYINT UNSIGNED NOT NULL CHECK (mes >= 1 AND mes <= 12) COMMENT 'Month data (1-12)',
    ano SMALLINT UNSIGNED NOT NULL COMMENT 'Year data',
    linhas_importadas SMALLINT UNSIGNED DEFAULT 0 COMMENT 'Number of lines successfully imported',
    status ENUM('PENDENTE', 'PROCESSANDO', 'SUCESSO', 'FALHA') DEFAULT 'PENDENTE'
        COMMENT 'Upload processing status',
    mensagem_erro TEXT DEFAULT NULL COMMENT 'Error message if status=FALHA',
    enviado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'File upload timestamp',
    processado_em DATETIME DEFAULT NULL COMMENT 'Processing completion timestamp',

    CONSTRAINT fk_uploads_area_id
        FOREIGN KEY (area_id) REFERENCES areas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_uploads_user_id
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_area_ano_mes (area_id, ano, mes),
    INDEX idx_status (status),
    INDEX idx_user_id (user_id),
    INDEX idx_arquivo_hash (arquivo_hash),
    INDEX idx_enviado_em (enviado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Upload history for DRE data files (XLSX)';

-- ============================================================================
-- Summary: Database Structure Created
-- Tables: 5 (areas, dre_linhas, users, dre_valores, uploads)
-- Seed Rows:
--   - areas: 8 rows
--   - dre_linhas: 11 rows
--   - users: 1 admin user (admin@dreceo.com / admin123)
-- Indexes: Composite indexes on (area_id, ano, mes) for performance
-- Constraints: Foreign keys with CASCADE, UNIQUE constraints, CHECKs
-- ============================================================================

-- Final verification query (should return 11)
-- SELECT COUNT(*) FROM dre_linhas;
