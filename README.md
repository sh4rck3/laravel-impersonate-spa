# Laravel Impersonate SPA

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sh4rck3/laravel-impersonate-spa.svg?style=flat-square)](https://packagist.org/packages/sh4rck3/laravel-impersonate-spa)
[![Total Downloads](https://img.shields.io/packagist/dt/sh4rck3/laravel-impersonate-spa.svg?style=flat-square)](https://packagist.org/packages/sh4rck3/laravel-impersonate-spa)
[![License](https://img.shields.io/packagist/l/sh4rck3/laravel-impersonate-spa.svg?style=flat-square)](https://packagist.org/packages/sh4rck3/laravel-impersonate-spa)

🎯 **Pacote Laravel para personificação de usuários compatível com SPA** - Funciona perfeitamente com Jetstream, Inertia.js, Vue 3 e Sanctum.

## ✨ Características

- 🎯 **Compatível com SPA**: Funciona perfeitamente com Inertia.js e Vue 3
- 🔐 **Sanctum Ready**: Compatibilidade total com autenticação Laravel Sanctum
- 🎨 **Componentes Vue**: Componentes Vue.js pré-construídos para integração fácil
- ⚡ **Baseado em Sessão**: Personificação simples e confiável baseada em sessão
- 🔧 **Instalação Automática**: Comandos Artisan para configuração automatizada
- 🎮 **Integração SweetAlert**: Diálogos de confirmação bonitos
- 🛡️ **Sistema de Permissões**: Verificação flexível de permissões
- 📱 **Design Responsivo**: Componentes de interface amigáveis para mobile

## 📋 Requisitos

Antes de instalar, certifique-se de que seu projeto Laravel tenha a **stack SPA completa**:

### ✅ Requisitos Obrigatórios:
- **PHP** 8.2 ou superior
- **Laravel** 11.0 ou superior
- **Laravel Jetstream** com Inertia.js
- **Inertia.js** 1.0 ou superior
- **Vue 3**
- **Laravel Sanctum** (incluído no Jetstream)

### 📦 Stack SPA Recomendada:
```bash
# Criar projeto Laravel
composer create-project laravel/laravel meu-projeto

# Instalar Jetstream com Inertia + Vue
composer require laravel/jetstream
php artisan jetstream:install inertia

# Instalar dependências frontend
npm install && npm run build

# Configurar banco
php artisan migrate

# (Opcional) Instalar Spatie Permission para controle de permissões
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# (Opcional) Instalar SweetAlert2 para notificações bonitas
npm install vue-sweetalert2 sweetalert2
```

### ⚠️ Importante:
Este pacote foi **desenvolvido especificamente para projetos SPA** com Jetstream + Inertia + Vue. Não funcionará corretamente em:
- ❌ Projetos Laravel tradicionais com Blade
- ❌ Projetos sem Inertia.js
- ❌ Projetos sem Sanctum
- ❌ SPAs com outras tecnologias (React, Angular, etc.)

## 🚀 Instalação

### 1. Instalar via Composer

```bash
composer require sh4rck3/laravel-impersonate-spa
```

### 2. Executar Instalação Automática

```bash
php artisan impersonate-spa:install
```

Este comando irá:
- ✅ Publicar o arquivo de configuração
- ✅ Registrar o middleware automaticamente
- ✅ Adicionar métodos necessários ao seu User model
- ✅ Configurar flash messages para Inertia
- ✅ Publicar componentes Vue.js

### 3. Limpar Cache

```bash
php artisan config:clear
```

## ⚙️ Configuração

### 1. Métodos do User Model

O comando de instalação adiciona automaticamente estes métodos ao seu `app/Models/User.php`:

```php
/**
 * Verifica se este usuário pode personificar outros usuários.
 */
public function canImpersonate(): bool
{
    // Verificar permissão específica
    return $this->can('user_impersonate');
    
    // Ou verificar role específica
    // return $this->hasRole('Administrator');
}

/**
 * Verifica se este usuário pode ser personificado.
 */
public function canBeImpersonated(): bool
{
    // Impedir super admins de serem personificados
    return !$this->hasRole('Super Admin');
    
    // Ou verificar status do usuário
    // return $this->status === 'active';
}
```

### 2. Adicionar Banner ao Layout

Adicione o banner ao seu layout principal (ex: `AppLayout.vue`):

```vue
<template>
    <div>
        <!-- Banner de personificação (colocar no topo do layout) -->
        <ImpersonationBanner />
        
        <!-- Seu layout existente -->
        <div class="min-h-screen bg-gray-100">
            <!-- Navegação, conteúdo, etc. -->
        </div>
    </div>
</template>

<script setup>
import ImpersonationBanner from '@/vendor/impersonate-spa/ImpersonationBanner.vue'
// ou se você copiou o componente para sua pasta de componentes:
// import ImpersonationBanner from '@/Components/ImpersonationBanner.vue'
</script>
```

### 3. Adicionar Botões de Personificação

Em seu componente de gerenciamento de usuários:

```vue
<template>
    <div class="user-list">
        <div v-for="user in users" :key="user.id" class="user-card">
            <div>
                <h3>{{ user.name }}</h3>
                <p>{{ user.email }}</p>
            </div>
            
            <!-- Botão de personificar -->
            <ImpersonateButton 
                :user-id="user.id"
                :user-name="user.name"
                v-if="$can('user_impersonate') && user.id !== $page.props.auth.user.id"
            />
        </div>
    </div>
</template>

<script setup>
import ImpersonateButton from '@/vendor/impersonate-spa/ImpersonateButton.vue'
</script>
```

## 🎮 Uso

### Funções Helper

```php
// Verificar se está personificando
if (is_impersonating()) {
    // Usuário está personificando alguém
}

// Obter ID do usuário personificado
$impersonatedUserId = get_impersonated_user_id();

// Obter ID do usuário original
$originalUserId = get_original_user_id();
```

### Dados Compartilhados do Frontend

O pacote compartilha automaticamente estes dados com Inertia:

```javascript
// Disponível em todos os componentes Vue
$page.props.isImpersonating        // boolean
$page.props.impersonatedUser       // { id, name, email } ou null
```

## 🎨 Componentes Vue

### ImpersonationBanner

Exibe um banner de aviso quando personificando com informações do usuário e botão de parar.

```vue
<ImpersonationBanner />
```

### ImpersonateButton

Botão personalizável para iniciar personificação.

**Props:**

- `userId` (Number|String, obrigatório): ID do usuário para personificar
- `userName` (String): Nome do usuário para diálogo de confirmação
- `label` (String): Texto do botão
- `variant` (String): Estilo do botão - 'primary', 'secondary', 'danger'
- `size` (String): Tamanho do botão - 'xs', 'sm', 'md', 'lg'
- `disabled` (Boolean): Desabilitar o botão

**Exemplos:**

```vue
<!-- Uso básico -->
<ImpersonateButton :user-id="123" user-name="João Silva" />

<!-- Estilo customizado -->
<ImpersonateButton 
    :user-id="456"
    user-name="Maria Santos"
    variant="secondary"
    size="lg"
    label="Personificar Usuário"
/>

<!-- Com verificação de permissão -->
<ImpersonateButton 
    :user-id="user.id"
    :user-name="user.name"
    :disabled="!$can('user_impersonate')"
    v-if="user.id !== $page.props.auth.user.id"
/>
```

## ⚙️ Configuração Avançada

Publique o arquivo de configuração para personalização:

```bash
php artisan vendor:publish --tag=impersonate-spa-config
```

```php
// config/impersonate-spa.php

return [
    // Model do usuário
    'user_model' => 'App\Models\User',
    
    // Chaves da sessão
    'session_key' => 'impersonate',
    'original_user_key' => 'original_user_id',
    
    // Configuração de rotas
    'route_prefix' => 'impersonate',
    'middleware' => ['auth:sanctum', 'verified'],
    
    // Rotas de redirecionamento
    'redirect_after_start' => 'dashboard',
    'redirect_after_stop' => 'dashboard',
    
    // Mensagens personalizadas
    'messages' => [
        'start_success' => 'Personificação iniciada com sucesso para :name!',
        'stop_success' => 'Personificação finalizada com sucesso!',
    ],
];
```

## 🎮 Integração com SweetAlert2

O pacote se integra automaticamente com SweetAlert2 se disponível:

```bash
# Instalar SweetAlert2
npm install vue-sweetalert2 sweetalert2

# Registrar no app.js
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

app.use(VueSweetalert2)
```

## 🛡️ Segurança

### Considerações Importantes:

1. **Sempre validar permissões** antes de permitir personificação
2. **Registrar atividades de personificação** para auditoria
3. **Limitar escopo de personificação** com restrições de role adequadas
4. **Usar HTTPS** em ambientes de produção
5. **Revisões regulares de permissões** para usuários com direitos de personificação

### Exemplo de Log de Auditoria:

```php
// No seu User model ou Service
public function logImpersonation($targetUserId, $action = 'start')
{
    Log::info('User Impersonation', [
        'original_user_id' => auth()->id(),
        'target_user_id' => $targetUserId,
        'action' => $action,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'timestamp' => now(),
    ]);
}
```

## 🧪 Comandos Disponíveis

```bash
# Instalação completa
php artisan impersonate-spa:install

# Instalar sem banner
php artisan impersonate-spa:install --no-banner

# Instalar sem registrar middleware automaticamente
php artisan impersonate-spa:install --no-middleware

# Publicar apenas configuração
php artisan impersonate-spa:publish --config

# Publicar apenas componentes
php artisan impersonate-spa:publish --components

# Publicar apenas stubs
php artisan impersonate-spa:publish --stubs

# Publicar tudo
php artisan impersonate-spa:publish --all
```

## 🔧 Solução de Problemas

### Problemas Comuns:

**Middleware não funcionando:**
- Certifique-se de que o middleware está registrado na ordem correta
- Execute: `php artisan config:clear`

**Componentes não encontrados:**
- Execute: `php artisan impersonate-spa:publish --components`
- Verifique os caminhos de importação nos seus arquivos Vue

**Flash messages não aparecendo:**
- Verifique a configuração de flash message no HandleInertiaRequests
- Confirme a instalação do SweetAlert2

**Erros de permissão:**
- Implemente os métodos `canImpersonate()` e `canBeImpersonated()` no User model
- Verifique as permissões/roles do usuário

## 📝 Changelog

Veja [CHANGELOG.md](CHANGELOG.md) para mais informações sobre as mudanças recentes.

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor, veja [CONTRIBUTING.md](CONTRIBUTING.md) para detalhes.

## 🔒 Vulnerabilidades de Segurança

Se você descobrir uma vulnerabilidade de segurança, por favor envie um e-mail para sh4rck3@example.com.

## 👨‍💻 Créditos

- [sh4rck3](https://github.com/sh4rck3)
- [Todos os Contribuidores](../../contributors)

## 📄 Licença

The MIT License (MIT). Veja [License File](LICENSE.md) para mais informações.

---

## 🎯 Exemplo Completo de Instalação

```bash
# 1. Criar projeto Laravel com Jetstream
composer create-project laravel/laravel meu-projeto
cd meu-projeto
composer require laravel/jetstream
php artisan jetstream:install inertia
npm install && npm run build

# 2. Configurar banco e migrar
php artisan migrate

# 3. (Opcional) Instalar Spatie Permission
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# 4. (Opcional) Instalar SweetAlert2
npm install vue-sweetalert2 sweetalert2

# 5. Instalar o pacote de personificação
composer require sh4rck3/laravel-impersonate-spa
php artisan impersonate-spa:install
php artisan config:clear

# 6. Pronto! Configurar permissões e usar os componentes
```

🎉 **Agora você tem personificação funcionando perfeitamente no seu projeto SPA!**