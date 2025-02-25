<?php

namespace CodeZero\LocalizedRoutes\Tests\Unit;

use CodeZero\LocalizedRoutes\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class RedirectToLocalizedTest extends TestCase
{
    /** @test */
    public function it_redirects_to_the_localized_url()
    {
        $this->withoutExceptionHandling();
        $this->setSupportedLocales(['en', 'nl']);
        $this->setUseLocaleMiddleware(false);
        $this->setRedirectToLocalizedUrls(true);

        Route::localized(function () {
            Route::get('/', function () {});
            Route::get('about', function () {});
        });

        Route::fallback(\CodeZero\LocalizedRoutes\Controller\FallbackController::class);

        $this->setAppLocale('en');
        $this->get('/')->assertRedirect('en');
        $this->get('en')->assertOk();
        $this->get('about')->assertRedirect('en/about');
        $this->get('en/about')->assertOk();

        $this->setAppLocale('nl');
        $this->get('/')->assertRedirect('nl');
        $this->get('nl')->assertOk();
        $this->get('about')->assertRedirect('nl/about');
        $this->get('nl/about')->assertOk();
    }

    /** @test */
    public function it_redirects_when_default_locale_slug_is_omitted()
    {
        $this->withoutExceptionHandling();
        $this->setSupportedLocales(['en', 'nl']);
        $this->setUseLocaleMiddleware(false);
        $this->setOmitUrlPrefixForLocale('en');
        $this->setRedirectToLocalizedUrls(true);

        Route::localized(function () {
            Route::get('/', function () {});
            Route::get('about', function () {});
        });

        Route::fallback(\CodeZero\LocalizedRoutes\Controller\FallbackController::class);

        $this->setAppLocale('en');
        $this->get('en')->assertRedirect('/');
        $this->get('/')->assertOk();
        $this->get('en/about')->assertRedirect('about');
        $this->get('about')->assertOk();

        $this->setAppLocale('nl');
        $this->get('nl')->assertOk();
        $this->get('nl/about')->assertOk();
    }

    /** @test */
    public function it_throws_404_and_does_not_redirect_if_no_localized_route_is_registered()
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setUseLocaleMiddleware(false);
        $this->setRedirectToLocalizedUrls(true);

        Route::fallback(\CodeZero\LocalizedRoutes\Controller\FallbackController::class);

        $this->setAppLocale('en');
        $this->get('missing')->assertNotFound();
    }
}
