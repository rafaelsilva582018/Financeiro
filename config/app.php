<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nome da Aplicação
    |--------------------------------------------------------------------------
    |
    | Este valor define o nome da sua aplicação, que será utilizado quando
    | o framework precisar exibir o nome do app em notificações ou
    | outros elementos da interface do usuário.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Ambiente da Aplicação
    |--------------------------------------------------------------------------
    |
    | Este valor determina o ambiente em que sua aplicação está rodando
    | atualmente (ex: local, staging, production). Isso pode influenciar
    | como alguns serviços são configurados. Defina no arquivo ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo de Depuração (Debug)
    |--------------------------------------------------------------------------
    |
    | Quando a aplicação está em modo debug, mensagens de erro detalhadas
    | com stack trace serão exibidas. Quando desativado, será mostrada
    | apenas uma página de erro genérica.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL da Aplicação
    |--------------------------------------------------------------------------
    |
    | Esta URL é usada pelo console para gerar URLs corretamente ao utilizar
    | comandos Artisan. Defina como a URL raiz da aplicação para garantir
    | que esteja disponível em todos os comandos.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Fuso Horário da Aplicação
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir o fuso horário padrão da aplicação, que será
    | utilizado pelas funções de data e hora do PHP.
    |
    */

    'timezone' => 'America/Sao_Paulo',

    /*
    |--------------------------------------------------------------------------
    | Configuração de Idioma (Locale)
    |--------------------------------------------------------------------------
    |
    | O locale define o idioma padrão utilizado pelos recursos de tradução
    | e localização do Laravel. Deve corresponder aos arquivos de idioma
    | existentes em "resources/lang".
    |
    */

    'locale' => env('APP_LOCALE', 'pt_BR'),

    /*
    |--------------------------------------------------------------------------
    | Idioma de Fallback
    |--------------------------------------------------------------------------
    |
    | Idioma utilizado caso uma tradução não seja encontrada no locale
    | principal configurado.
    |
    */

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'pt_BR'),

    /*
    |--------------------------------------------------------------------------
    | Idioma do Faker
    |--------------------------------------------------------------------------
    |
    | Define o idioma utilizado pela biblioteca Faker para geração de dados
    | falsos (ex: nomes, endereços, telefones).
    |
    */

    'faker_locale' => env('APP_FAKER_LOCALE', 'pt_BR'),

    /*
    |--------------------------------------------------------------------------
    | Chave de Criptografia
    |--------------------------------------------------------------------------
    |
    | Esta chave é utilizada pelos serviços de criptografia do Laravel e
    | deve ser uma string aleatória de 32 caracteres para garantir a
    | segurança dos dados criptografados.
    |
    | Gere esta chave antes de publicar a aplicação.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver do Modo de Manutenção
    |--------------------------------------------------------------------------
    |
    | Estas opções definem qual driver será utilizado para controlar o
    | status do "modo de manutenção" do Laravel.
    |
    | O driver "cache" permite controlar o modo de manutenção em múltiplos
    | servidores.
    |
    | Drivers suportados: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
