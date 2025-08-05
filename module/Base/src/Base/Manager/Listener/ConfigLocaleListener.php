<?php

namespace Base\Manager\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Stdlib\RequestInterface as Request;

class ConfigLocaleListener extends AbstractListenerAggregate
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function attach(EventManagerInterface $events)
    {
        $events->attach('prepare', array($this, 'onPrepare'));
    }

    public function onPrepare(Event $event)
    {
        if ($this->request instanceof HttpRequest) {

            $configManager = $event->getTarget();
            $configI18n = $configManager->need('i18n');

            $cookieNamePrefix = $configManager->need('cookie_config.cookie_name_prefix');
            $cookieName = $cookieNamePrefix . '-locale';

            // Get the configured default locale from i18n config
            $defaultLocale = isset($configI18n['locale']) ? $configI18n['locale'] : 'en-US';

            $locale = $this->request->getQuery('locale');

            // 1. Check if user explicitly chose a language via URL parameter
            if ($locale && isset($configI18n['choice'][$locale])) {
                $configManager->set('i18n.locale', $locale);
                setcookie($cookieName, $locale, time() + 1209600, '/');
            } else {
                // 2. Check if user previously chose a language (stored in cookie)
                if (isset($_COOKIE[$cookieName])) {
                    $locale = $_COOKIE[$cookieName];

                    if (isset($configI18n['choice'][$locale])) {
                        $configManager->set('i18n.locale', $locale);
                    } else {
                        // Cookie has invalid locale, use default and clear cookie
                        $configManager->set('i18n.locale', $defaultLocale);
                        setcookie($cookieName, '', time() - 3600, '/'); // Clear invalid cookie
                    }
                } else {
                    // 3. No explicit choice, use configured default
                    // (Don't auto-detect from browser unless default is not available)
                    if (isset($configI18n['choice'][$defaultLocale])) {
                        $configManager->set('i18n.locale', $defaultLocale);
                    } else {
                        // Default locale not available, try browser detection as fallback
                        $headers = $this->request->getHeaders();
                        $foundBrowserMatch = false;

                        if ($headers->has('Accept-Language')) {
                            $acceptedLocales = $headers->get('Accept-Language')->getPrioritized();

                            foreach ($acceptedLocales as $acceptedLocale) {
                                $acceptedLocaleParts = preg_split('/[\-\_]/', $acceptedLocale->getLanguage());
                                $acceptedLocalePart = $acceptedLocaleParts[0];

                                if (isset($configI18n['choice']) && is_array($configI18n['choice'])) {
                                    foreach ($configI18n['choice'] as $locale => $title) {
                                        $localeParts = preg_split('/[\-\_]/', $locale);
                                        $localePart = $localeParts[0];

                                        if ($localePart == $acceptedLocalePart) {
                                            $configManager->set('i18n.locale', $locale);
                                            $foundBrowserMatch = true;
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }

                        // If no browser match found, use first available locale
                        if (!$foundBrowserMatch && isset($configI18n['choice']) && is_array($configI18n['choice'])) {
                            $availableLocales = array_keys($configI18n['choice']);
                            if (!empty($availableLocales)) {
                                $configManager->set('i18n.locale', $availableLocales[0]);
                            }
                        }
                    }
                }
            }
        }
    }

}