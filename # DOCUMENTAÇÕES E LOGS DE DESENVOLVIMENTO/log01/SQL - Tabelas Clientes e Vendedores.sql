-- ========================================
-- Tabelas Clientes e Vendedores
-- Local: tabelas_clientes_vendedores.sql
-- ========================================

-- Tabela CLIENTES
CREATE TABLE IF NOT EXISTS clientes (
    cliente_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cliente_nome VARCHAR(150) NOT NULL COMMENT 'Nome completo ou razão social do cliente',
    cliente_nome_fantasia VARCHAR(100) COMMENT 'Nome fantasia (opcional)',
    cliente_cnpj_cpf VARCHAR(18) UNIQUE COMMENT 'CNPJ ou CPF do cliente',
    cliente_inscricao_estadual VARCHAR(20) COMMENT 'Inscrição estadual (opcional)',
    cliente_endereco VARCHAR(255) COMMENT 'Endereço completo',
    cliente_bairro VARCHAR(100) COMMENT 'Bairro',
    cliente_cidade VARCHAR(100) COMMENT 'Cidade',
    cliente_estado_id INT COMMENT 'FK para a tabela estados',
    cliente_cep VARCHAR(10) COMMENT 'CEP',
    cliente_telefone VARCHAR(20) COMMENT 'Telefone principal',
    cliente_telefone_secundario VARCHAR(20) COMMENT 'Telefone secundário',
    cliente_email VARCHAR(100) UNIQUE COMMENT 'E-mail principal',
    cliente_contato_principal VARCHAR(100) COMMENT 'Nome do contato principal',
    cliente_observacoes TEXT COMMENT 'Observações gerais sobre o cliente',
    cliente_status ENUM('ativo', 'inativo') DEFAULT 'ativo' COMMENT 'Status do cliente',
    cliente_criadoem DATETIME DEFAULT CURRENT_TIMESTAMP,
    cliente_atualizadoem DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Chave Estrangeira
    FOREIGN KEY (cliente_estado_id) REFERENCES estados(estado_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clientes do sistema';

-- Inserir alguns clientes de exemplo
INSERT INTO clientes (cliente_nome, cliente_nome_fantasia, cliente_cnpj_cpf, cliente_inscricao_estadual, cliente_endereco, cliente_bairro, cliente_cidade, cliente_estado_id, cliente_cep, cliente_telefone, cliente_telefone_secundario, cliente_email, cliente_contato_principal, cliente_observacoes, cliente_status) VALUES
('CLÍNICA MÉDICA VITA SAÚDE LTDA', 'CLÍNICA VITA SAÚDE', '12.345.678/0001-90', '123456789', 'AV. PAULISTA, 1000', 'BELA VISTA', 'SÃO PAULO', (SELECT estado_id FROM estados WHERE estado_uf = 'SP'), '01310-100', '(11) 3333-4444', '(11) 98888-7777', 'contato@clinicavitasaude.com.br', 'DR. JOÃO SILVA', 'Cliente prioritário', 'ativo'),
('MARIA SOUZA - ME', 'MARIA SOUZA', '987.654.321-00', NULL, 'RUA DAS FLORES, 123', 'JARDIM PRIMAVERA', 'RIO DE JANEIRO', (SELECT estado_id FROM estados WHERE estado_uf = 'RJ'), '20040-020', '(21) 2222-3333', NULL, 'maria.souza@email.com', 'MARIA SOUZA', 'Cliente regular', 'ativo'),
('HOSPITAL REGIONAL CENTRAL', NULL, '55.666.777/0001-88', '987654321', 'RUA DOUTOR CARLOS, 500', 'CENTRO', 'BELO HORIZONTE', (SELECT estado_id FROM estados WHERE estado_uf = 'MG'), '30130-010', '(31) 4444-5555', '(31) 99999-8888', 'adm@hospitalcentral.com.br', 'ENF. ANA OLIVEIRA', 'Hospital parceiro', 'ativo'),
('CONSULTÓRIO DO DR. PEDRO ALVES', 'DR. PEDRO ALVES', NULL, NULL, 'AV. BRASIL, 2000', 'BARRO DO JENIPAPO', 'SALVADOR', (SELECT estado_id FROM estados WHERE estado_uf = 'BA'), '40000-000', '(71) 5555-6666', NULL, 'dr.pedro.alves@gmail.com', 'DR. PEDRO ALVES', 'Consultório independente', 'ativo');

-- Tabela VENDEDORES
CREATE TABLE IF NOT EXISTS vendedores (
    vendedor_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    vendedor_nome VARCHAR(150) NOT NULL COMMENT 'Nome completo do vendedor',
    vendedor_matricula VARCHAR(20) UNIQUE COMMENT 'Matrícula ou código interno do vendedor',
    vendedor_cpf VARCHAR(14) UNIQUE COMMENT 'CPF do vendedor',
    vendedor_endereco VARCHAR(255) COMMENT 'Endereço completo',
    vendedor_bairro VARCHAR(100) COMMENT 'Bairro',
    vendedor_cidade VARCHAR(100) COMMENT 'Cidade',
    vendedor_estado_id INT COMMENT 'FK para a tabela estados',
    vendedor_cep VARCHAR(10) COMMENT 'CEP',
    vendedor_telefone VARCHAR(20) COMMENT 'Telefone principal',
    vendedor_email VARCHAR(100) UNIQUE COMMENT 'E-mail corporativo',
    vendedor_comissao_percentual DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Percentual de comissão padrão',
    vendedor_data_admissao DATE COMMENT 'Data de admissão',
    vendedor_data_demissao DATE COMMENT 'Data de demissão (opcional)',
    vendedor_supervisor_id INT COMMENT 'FK para outro vendedor (supervisor)',
    vendedor_observacoes TEXT COMMENT 'Observações gerais sobre o vendedor',
    vendedor_status ENUM('ativo', 'inativo') DEFAULT 'ativo' COMMENT 'Status do vendedor',
    vendedor_criadoem DATETIME DEFAULT CURRENT_TIMESTAMP,
    vendedor_atualizadoem DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Chaves Estrangeiras
    FOREIGN KEY (vendedor_estado_id) REFERENCES estados(estado_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (vendedor_supervisor_id) REFERENCES vendedores(vendedor_id) ON DELETE SET NULL ON UPDATE CASCADE -- Auto-relacionamento
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vendedores do sistema';

-- Inserir alguns vendedores de exemplo
INSERT INTO vendedores (vendedor_nome, vendedor_matricula, vendedor_cpf, vendedor_endereco, vendedor_bairro, vendedor_cidade, vendedor_estado_id, vendedor_cep, vendedor_telefone, vendedor_email, vendedor_comissao_percentual, vendedor_data_admissao, vendedor_supervisor_id, vendedor_observacoes, vendedor_status) VALUES
('CARLOS ALBERTO SANTOS', 'VEN001', '111.222.333-44', 'RUA DAS ACÁCIAS, 456', 'JARDIM BOTÂNICO', 'CURITIBA', (SELECT estado_id FROM estados WHERE estado_uf = 'PR'), '80000-000', '(41) 99999-1111', 'carlos.santos@empresa.com.br', 5.00, '2023-01-15', NULL, 'Vendedor experiente', 'ativo'),
('ANA MARIA SILVA', 'VEN002', '555.666.777-88', 'AV. DAS AMÉRICAS, 1001', 'BARRA DA TIJUCA', 'RIO DE JANEIRO', (SELECT estado_id FROM estados WHERE estado_uf = 'RJ'), '22000-000', '(21) 98888-7777', 'ana.silva@empresa.com.br', 4.50, '2023-03-20', 1, 'Nova contratada', 'ativo'),
('JOÃO BATISTA OLIVEIRA', 'VEN003', '999.888.777-66', 'RUA DO OURO, 234', 'OURO PRETO', 'BELO HORIZONTE', (SELECT estado_id FROM estados WHERE estado_uf = 'MG'), '30000-000', '(31) 97777-6666', 'joao.oliveira@empresa.com.br', 6.00, '2022-11-10', 1, 'Especialista em grandes contas', 'ativo'),
('FERNANDA COSTA', 'VEN004', '444.333.222-11', 'AV. PAULISTA, 2000', 'BELA VISTA', 'SÃO PAULO', (SELECT estado_id FROM estados WHERE estado_uf = 'SP'), '01310-200', '(11) 96666-5555', 'fernanda.costa@empresa.com.br', 5.50, '2023-05-01', 2, 'Atendimento ao cliente', 'ativo');