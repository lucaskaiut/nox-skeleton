<?php

/**
 * AiPublisher — Configuração editorial do projeto (PROJECT-specific).
 *
 * Os valores aqui são específicos da Nox.
 * A infraestrutura (AiPublisherService, SchemaGenerator, etc.) é CORE.
 */
return [

    'default_status' => env('AI_PUBLISHER_DEFAULT_STATUS', 'draft'),

    'min_content_length' => (int) env('AI_PUBLISHER_MIN_CONTENT_LENGTH', 200),

    'editorial' => [

        'company' => env('AI_EDITORIAL_COMPANY', 'Nox Soluções em Tecnologia'),

        'audience' => explode(',', env('AI_EDITORIAL_AUDIENCE', 'empresas,empreendedores,gestores')),

        'tone' => env('AI_EDITORIAL_TONE', 'profissional'),

        'content_rules' => [
            'Usar headings H2 e H3 para estruturar o conteúdo.',
            'Gerar meta description entre 120 e 320 caracteres.',
            'Gerar resumo (excerpt) com até 400 caracteres.',
            'Evitar conteúdo superficial ou genérico.',
            'Incluir exemplos práticos sempre que possível.',
            'Manter parágrafos com no máximo 4-5 linhas.',
            'Usar linguagem clara e objetiva.',
        ],

    ],
];
