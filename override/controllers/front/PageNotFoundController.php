<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */
class PageNotFoundController extends PageNotFoundControllerCore
{
    /*
    * module: everpsseo
    * date: 2022-10-30 16:02:55
    * version: 8.4.2
    */
    public function initContent()
    {
        require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoRedirect.php';
        if ((bool)Configuration::get('EVERSEO_REWRITE') === true) {
            $redirectionCode = (int)Configuration::get('EVERSEO_REDIRECT');
            switch ($redirectionCode) {
                case 301:
                    $redirectionCode = 'Status: 301 Moved Permanently, false, 301';
                    break;
                case 302:
                    $redirectionCode = null;
                    break;
                case 303:
                    $redirectionCode = 'HTTP/1.1 303 See Other';
                    break;
                case 307:
                    $redirectionCode = 'HTTP/1.1 307 Temporary Redirect';
                    break;
                default:
                    $redirectionCode = 'Status: 301 Moved Permanently, false, 301';
                    break;
            }
            $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $notFoundRedirection = EverPsSeoRedirect::ifNotFoundExists(
                $url,
                (int)$this->context->shop->id
            );
            if (Validate::isLoadedObject($notFoundRedirection)
                && Validate::isUrl($notFoundRedirection->redirection)
                && (bool)$notFoundRedirection->active === true
            ) {
                $redirectExists = $notFoundRedirection->redirection;
                $redirectionCode = $notFoundRedirection->getRedirectionStatusCode(
                    (int)$notFoundRedirection->code
                );
            } else {
                $redirectExists = EverPsSeoRedirect::getRedirectUrl(
                    $url,
                    (int)$this->context->shop->id,
                    (int)$this->context->language->id
                );
            }
            if (Validate::isUrl($redirectExists)) {
                Tools::redirect(
                    $redirectExists,
                    __PS_BASE_URI__,
                    null,
                    $redirectionCode
                );
            } else {
                if ((bool)Configuration::get('EVERSEO_NOT_FOUND') === true) {
                    Tools::redirect('index.php');
                } else {
                    if ((bool)Configuration::get('EVERSEO_CUSTOM_404') === true) {
                        Tools::redirect(
                            $this->context->link->getModuleLink(
                                'everpsseo',
                                'everpagenotfound'
                            )
                        );
                    } else {
                        header('HTTP/1.1 404 Not Found');
                        header('Status: 404 Not Found');
                        $this->context->cookie->disallowWriting();
                        parent::initContent();
                        $this->setTemplate('errors/404');
                    }
                }
            }
        } else {
            if ((bool)Configuration::get('EVERSEO_CUSTOM_404') === true) {
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'everpsseo',
                        'everpagenotfound'
                    )
                );
            } else {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                $this->context->cookie->disallowWriting();
                parent::initContent();
                $this->setTemplate('errors/404');
            }
        }
    }
}
