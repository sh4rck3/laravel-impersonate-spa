<?php

namespace Sh4rck3\LaravelImpersonateSpa\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'impersonate-spa:install 
                            {--no-banner : Skip adding the impersonation banner}
                            {--no-middleware : Skip middleware registration}';

    /**
     * The console command description.
     */
    protected $description = 'Install Laravel Impersonate SPA package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing Laravel Impersonate SPA...');

        // 1. Publish config
        $this->publishConfig();

        // 2. Add methods to User model
        $this->addUserModelMethods();

        // 3. Register middleware (if not skipped)
        if (!$this->option('no-middleware')) {
            $this->registerMiddleware();
        }

        // 4. Publish resources
        $this->publishResources();

        // 5. Add Inertia flash messages
        $this->configureInertiaFlashMessages();

        $this->info('✅ Laravel Impersonate SPA installed successfully!');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Add the ImpersonationBanner component to your layout');
        $this->line('2. Use ImpersonateButton in your user management views');
        $this->line('3. Configure user permissions in canImpersonate() and canBeImpersonated()');
        $this->newLine();
        $this->line('📖 Check the documentation for more details!');
    }

    protected function publishConfig()
    {
        $this->info('Publishing configuration...');
        
        $this->call('vendor:publish', [
            '--tag' => 'impersonate-spa-config',
            '--force' => true,
        ]);
    }

    protected function publishResources()
    {
        $this->info('Publishing Vue components...');
        
        $this->call('vendor:publish', [
            '--tag' => 'impersonate-spa-resources',
            '--force' => true,
        ]);
    }

    protected function addUserModelMethods()
    {
        $this->info('Adding methods to User model...');

        $userModelPath = app_path('Models/User.php');
        
        if (!File::exists($userModelPath)) {
            $this->error('User model not found at ' . $userModelPath);
            return;
        }

        $userModel = File::get($userModelPath);

        // Check if methods already exist
        if (Str::contains($userModel, 'canImpersonate') && Str::contains($userModel, 'canBeImpersonated')) {
            $this->line('User model methods already exist. Skipping...');
            return;
        }

        // Add methods before the last closing brace
        $methods = '
    /**
     * Verifica se este usuário pode personificar outros usuários.
     */
    public function canImpersonate(): bool
    {
        // Verificar permissão específica (requer Spatie Permission)
        // return $this->can(\'user_impersonate\');
        
        // Ou verificar role específica
        // return $this->hasRole(\'Administrator\');
        
        // Por padrão, apenas verificar se é admin (ajuste conforme necessário)
        return $this->email === \'admin@example.com\'; // ALTERE ISSO!
    }

    /**
     * Verifica se este usuário pode ser personificado.
     */
    public function canBeImpersonated(): bool
    {
        // Impedir super admins de serem personificados
        // return !$this->hasRole(\'Super Admin\');
        
        // Ou verificar status do usuário
        // return $this->status === \'active\';
        
        // Por padrão, permitir personificação (ajuste conforme necessário)
        return true;
    }
';

        // Find the last closing brace and add methods before it
        $userModel = Str::replaceLast('}', $methods . '}', $userModel);

        File::put($userModelPath, $userModel);
        
        $this->line('✅ Methods added to User model');
        $this->warn('⚠️  Please review and customize the canImpersonate() and canBeImpersonated() methods!');
    }

    protected function registerMiddleware()
    {
        $this->info('Registering middleware...');

        $kernelPath = app_path('Http/Kernel.php');
        
        if (!File::exists($kernelPath)) {
            $this->line('Kernel.php not found. You may need to register middleware manually in bootstrap/app.php');
            return;
        }

        $kernel = File::get($kernelPath);

        $middlewareAlias = "'impersonate' => \Sh4rck3\LaravelImpersonateSpa\Http\Middleware\ImpersonateMiddleware::class,";

        if (Str::contains($kernel, 'ImpersonateMiddleware')) {
            $this->line('Middleware already registered. Skipping...');
            return;
        }

        // Add to middlewareAliases array
        if (Str::contains($kernel, 'protected $middlewareAliases = [')) {
            $kernel = Str::replace(
                'protected $middlewareAliases = [',
                "protected \$middlewareAliases = [\n        " . $middlewareAlias,
                $kernel
            );

            File::put($kernelPath, $kernel);
            $this->line('✅ Middleware registered');
        } else {
            $this->warn('Could not automatically register middleware. Please add manually:');
            $this->line($middlewareAlias);
        }
    }

    protected function configureInertiaFlashMessages()
    {
        $this->info('Configuring Inertia flash messages...');

        $handleInertiaPath = app_path('Http/Middleware/HandleInertiaRequests.php');
        
        if (!File::exists($handleInertiaPath)) {
            $this->warn('HandleInertiaRequests.php not found. Please configure flash messages manually.');
            return;
        }

        $handleInertia = File::get($handleInertiaPath);

        // Check if impersonation data is already shared
        if (Str::contains($handleInertia, 'isImpersonating') || Str::contains($handleInertia, 'impersonatedUser')) {
            $this->line('Inertia configuration already exists. Skipping...');
            return;
        }

        // Add to share method
        $shareAddition = "
            // Impersonation data
            'isImpersonating' => is_impersonating(),
            'impersonatedUser' => is_impersonating() ? [
                'id' => get_impersonated_user_id(),
                'name' => optional(app(config('impersonate-spa.user_model'))->find(get_impersonated_user_id()))->name,
                'email' => optional(app(config('impersonate-spa.user_model'))->find(get_impersonated_user_id()))->email,
            ] : null,";

        // Find the return array in share method and add before the closing bracket
        $pattern = "/(return array_merge\(parent::share\(\$request\), \[.*?)(\s*\]\);)/s";
        
        if (preg_match($pattern, $handleInertia)) {
            $handleInertia = preg_replace(
                $pattern,
                "$1$shareAddition$2",
                $handleInertia
            );

            File::put($handleInertiaPath, $handleInertia);
            $this->line('✅ Inertia configuration updated');
        } else {
            $this->warn('Could not automatically configure Inertia. Please add manually to HandleInertiaRequests.php:');
            $this->line($shareAddition);
        }
    }
}