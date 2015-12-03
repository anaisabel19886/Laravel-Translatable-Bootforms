<?php namespace Propaganistas\LaravelTranslatableBootForms;

use Illuminate\Support\ServiceProvider;

class TranslatableBootFormsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('translatable-bootforms.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'translatable-bootforms'
        );

        $locales = with(new Translatable\TranslatableWrapper)->getLocales();

        $this->app['translatableBootform.form.builder'] = $this->app->share(function ($app) use ($locales) {
            $formBuilder = new Form\FormBuilder();
            $formBuilder->setLocales($locales);
            $formBuilder->setErrorStore($app['adamwathan.form.errorstore']);
            $formBuilder->setOldInputProvider($app['adamwathan.form.oldinput']);
            $formBuilder->setToken($app['session.store']->getToken());

            return $formBuilder;
        });

        $this->app['translatableBootform'] = $this->app->share(function ($app) use ($locales) {
            $form = new TranslatableBootForm($app['bootform']);
            $form->locales($locales);

            return $form;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'translatableBootform.form.builder',
            'translatableBootform',
        );
    }

}
