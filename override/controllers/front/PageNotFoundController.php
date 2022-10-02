<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

class PageNotFoundController extends PageNotFoundControllerCore
{
    public function initContent()
    {
        require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoRedirect.php';
        if ((bool)Configuration::get('EVERSEO_REWRITE') === true) {
            $redirCode = (int)Configuration::get('EVERSEO_REDIRECT');
            switch ($redirCode) {
                case 301:
                    $redirCode = 'Status: 301 Moved Permanently, false, 301';
                    break;

                case 302:
                    $redirCode = null;
                    break;

                case 303:
                    $redirCode = 'HTTP/1.1 303 See Other';
                    break;

                case 307:
                    $redirCode = 'HTTP/1.1 307 Temporary Redirect';
                    break;
                
                default:
                    $redirCode = 'Status: 301 Moved Permanently, false, 301';
                    break;
            }
            if (isset($_SERVER['HTTP_REFERER'])) {
                $from = $_SERVER['HTTP_REFERER'];
            } else {
                $from = 'null';
            }
            $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $splitted = preg_split("#/#", parse_url($url, PHP_URL_PATH));
            $notFoundExists = EverPsSeoRedirect::ifNotFoundExists(
                $url,
                (int)$this->context->shop->id,
                (int)$this->context->language->id,
                $from
            );
            if ($notFoundExists) {
                $redirectExists = EverPsSeoRedirect::ifRedirectExists(
                    $url,
                    (int)$this->context->shop->id
                );
            } else {
                $redirectExists = false;
            }
            if (!$redirectExists) {
                $redirect = EverPsSeoRedirect::getRedirectUrl(
                    $splitted,
                    (int)$this->context->shop->id,
                    (int)$this->context->language->id
                );
                if ($redirect) {
                    Tools::redirect($redirect, __PS_BASE_URI__, null, $redirCode);
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
                Tools::redirect($redirectExists, __PS_BASE_URI__, null, $redirCode);
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
