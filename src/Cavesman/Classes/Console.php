<?php

namespace src\Modules;

use Cavesman\Config;
use Cavesman\Console;
use Cavesman\DB;
use Cavesman\Display;
use Cavesman\Http;
use Cavesman\Modules;
use Cavesman\Router;
use Cavesman\Smarty;
use DateTimeInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DOMDocument;
use DOMXPath;
use Exception;
use FluentDOM\DOM\Element;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\ParserException;
use src\Modules\Client\Entity\ClientEntity;
use src\Modules\User\Session;
use src\Modules\Webscraping\Entity\DomainEntity;
use src\Modules\WebscrapingBucle\Entity\ScraperLogsEntity;
use src\Modules\WebscrapingBucle\Entity\VideoProducerWorkEntity;
use src\Modules\WebscrapingBucle\Entity\WebscrapingEntity;
use src\Modules\WebscrapingBucle\Entity\WebscrapingTemplateEntity;


class WebscrapingBucle extends Modules
{

    public static $config = [];
    private const URL_LOGOS = '/logos/webscraping-bucle/';
    private const URL_DEFAULT_IMAGES = '/defaultImages/webscraping-bucle/';
    private const URLS = [
        'local' => 'http://localhost:8091',
        'dev' => 'https://web-scrapper-k4hg0.tappx.net',
        'prod' => 'https://web-scrapper.tappx.com',
    ];


    public static function router()
    {
        Router::get(_PATH_ . self::trans(self::$config['name'] . "-slug"), self::class . '@render');

        // App routes
        Router::mount(_PATH_ . 'webscraping-bucle', function () {
            Router::middleware('POST|GET', '/(.*)', function () {
                if (!Session::isLogged())
                    Http::redirect(_PATH_ . 'user/logout');
            });
            Router::get('/add-form', self::class . '@addForm');
            Router::get('/(\d+)', self::class . '@edit');
            Router::get('/html/(\d+)', self::class . '@getHtmlPage');
        });

        Router::mount(_PATH_ . "api/webscraping-bucle/template", function () {
            Router::post('/', self::class . '@apiSaveTemplate');
            Router::get('/{id}', self::class . '@apiGetTemplate');
        });
        // Api Routes
        Router::mount(_PATH_ . "api/webscraping-bucle", function () {
            Router::post('/search', self::class . '@apiSearch');
            Router::get('/{id}/data', self::class . '@apiDownloadData');
            Router::get('/{id}', self::class . '@apiEdit');
            Router::post('/delete/{id}', self::class . '@apiDelete');
            Router::post('/active/{id}', self::class . '@apiActive');
            Router::post('/processed/{id}', self::class . '@apiProcessed');

            // Validation Routes
            Router::mount('/validate', function () {
                Router::post('/url', self::class . '@apiParseUrl');
            });

            // Validation Routes

        });

        Router::mount(_PATH_ . 'cron', function () {
            Router::get('/webscraping-bucle/process-urls', self::class . '@cronProcessUrls');

        });
        Console::command('webscraping:bucle:process-url', self::class . '@cronProcessUrls');
    }

    public static function __install()
    {
        if (!is_dir(_CACHE_ . '/tmp/webscraping-bucle')) {
            mkdir(_CACHE_ . '/tmp/webscraping-bucle', 0777, true);
        }

        if (!is_dir(_WEB_ . '/c/webscraping-bucle')) {
            mkdir(_WEB_ . '/c/webscraping-bucle', 0777, true);
        }
    }

    public static function menu(): array
    {
        return array(

            "items" => array(
                array(
                    "order" => 0,
                    "name" => "webscraping-bucle",
                    "label" => self::trans("WebScraping Bucle"),
                    "icon" => "fa fa-search fa-2x",
                    "link" => _PATH_ . self::trans(self::$config['name'] . "-slug"),
                    "permission" => [
                        "action" => "view_webscrapping",
                        "group" => "ACCESS_PERMISSION",
                    ],
                    "childs" => [],
                )
            )
        );
    }

    private static function getDomFromUrl($item)
    {

        $url = 'http://' . $item->getDomain()->getHost() . $item->getPath();

        try {
            $web = self::downloadUrl($url, true);
        } catch (Exception $e) {
            $url = 'https://' . $item->getDomain()->getHost() . $item->getPath();
            $web = self::downloadUrl($url, true);
        }

        $web = str_replace("amp-img", "img", $web);
        $web = str_replace("</img>", "", $web);

        $dom = \FluentDOM::QueryCss(
            $web,
            'text/html',
            [
                \FluentDOM\Loader\Options::ALLOW_FILE => true,
            ]
        );
        $link_tags = $dom->find('link');

        $css = [];


        $script = $dom->find('script');

        $remove = [];
        foreach ($script as $itm) {
            $remove[] = $itm;
        }

        foreach ($remove as $itm) {
            $itm->parentNode->removeChild($itm);
        }

        $csss = $dom->find('link');

        $remove = [];
        foreach ($csss as $itm) {
            $remove[] = $itm;
        }

        foreach ($remove as $itm) {
            $itm->parentNode->removeChild($itm);
        }

        $body = $dom->find('body');

        return $body;
    }

    public static function cronProcessUrls()
    {
        Console::clean();

        Console::show('INICIO PROCESADO DE URLS', Console::INFO);
        Console::show('MEMORIA ACTUAL: ' . round(memory_get_usage() / 1048576, 2) . "MB", Console::INFO);
        // TODO: Poner un validador de token o permiso para usuario

        $process_urls = false;
        // Get db entoty manager
        $em = DB::getManager();

        /** @var WebscrapingEntity[] $urls */
        $urls = $em->getRepository(WebscrapingEntity::class)->findBy(["processed" => 0, "active" => true]);
        $processed = [];
        foreach ($urls as $key => $url) {

            Console::progress($key, count($urls) );

            $web = 'https://' . $url->getDomain()->getHost() . $url->getPath();

            Console::show("URL: " . $web);

            $data = [
                "url" => $web,
                'data' => [],
            ];
            try {

                if (!$url->getTemplate()) {
                    Log::add("error", "webscraping", $web, self::trans("NO HAY PLANTILLA ASIGNADA"));
                    Console::show(self::trans('NO HAY PLANTILLA ASIGNADA'), CONSOLE::ERROR);
                    $process_urls = true;
                    continue;
                }

                Console::show("TEMPLATE: " . $url->getTemplate()->getName());


                // Si la template no está configurada nos ahorramos el proceso
                if (empty($url->getTemplate()->getData())) {
                    Console::show("TEMPLATE NO CONFIGURADA", Console::WARNING);
                    continue;
                }

                $document = self::getDomFromUrl($url);

                if (!$document) {
                    Console::show("Error al procresar DOM", Console::WARNING);
                    continue;
                }

                if (!is_object($document)) {
                    Console::show("DOM NO ES UN Object", Console::WARNING);
                    continue;
                }

                foreach ($url->getTemplate()->getData() as $section) {

                    $text = [];
                    if ($section['type'] == 'image' && $url->getTemplate()->getUseDefaultImage() && $url->getTemplate()->getDefaultImage()) {
                        $text[] = self::URLS[$_ENV['ENV']] . $url->getTemplate()->getDefaultImage();
                        $data['data'][$section['name']] = $text;
                        continue;
                    }

                    foreach ($section['identifier'] as $key => $selector) {

                        Console::progress($key, count($section['identifier']) );

                        $doc = clone $document;

                        // Delete ignored
                        //if(!empty($url->getTemplate()->getIgnored()))
                        //$doc->find(implode(', ', $url->getTemplate()->getIgnored()))->remove();


                        if (!empty($selector['selector'])) {
                            //Console::show("SELECTOR: " . PHP_EOL . $selector['selector']);
                            $selector_sump = '';
                            foreach (explode(">", $selector['selector']) as $key => $selector1) {
                                if ($key)
                                    $selector_sump .= " > ";
                                $selector_sump .= trim($selector1);
                                try {
                                    $doc = $doc->find($selector_sump, 1);
                                } catch (Exception $e) {
                                    Console::show('Falla en selector: ' . $selector_sump, Console::ERROR);
                                    Log::add("error", "webscraping", $web, self::trans("Falla selector: ") . '<i class="fa fa-info-circle" title="' . $selector_sump . '"></i>');

                                    continue 2;
                                }
                            }
                            // FIXME: Hay un problema con los selectores de picture y source mal escritos...
                            try {
                                $docs = $doc->find($selector_sump, 1);
                            } catch (Exception $e) {
                                Console::show(self::trans('HA FALLADO LA CARGA DE DATOS, O BIEN HAY ATRIBUTOS SIN MARCAR O DATOS SIN RELLENAR. DETALLES: '), CONSOLE::ERROR);
                                Console::show($e->getMessage(), Console::WARNING);
                                Console::show("Line: " . __LINE__, Console::INFO);
                                Log::add("error", "webscraping", $web, self::trans("HA FALLADO LA CARGA DE DATOS, O BIEN HAY ATRIBUTOS SIN MARCAR O DATOS SIN RELLENAR. DETALLES: ") . '<i class="fa fa-info-circle" title="' . $e->getMessage() . '"></i>');
                                continue;
                            }

                        } else {

                            // Hacemos test a cada uno de los elementos para asegurarnos que no falla
                            $selector_sump = '';
                            foreach (explode(">", $selector) as $key => $selector1) {
                                if ($key)
                                    $selector_sump .= " > ";
                                $selector_sump .= trim($selector1);
                                try {
                                    $doc->find($selector_sump, 1);
                                } catch (Exception $e) {
                                    Console::show('Falla en selector: ' . $selector_sump, Console::ERROR);
                                    Log::add("error", "webscraping", $web, self::trans("Falla selector: ") . '<i class="fa fa-info-circle" title="' . $selector_sump . '"></i>');

                                    continue 2;
                                }
                            }

                            try {
                                $docs = $doc->find($selector_sump, 3);
                            } catch (Exception $e) {
                                Console::show(self::trans('HA FALLADO LA CARGA DE DATOS, O BIEN HAY ATRIBUTOS SIN MARCAR O DATOS SIN RELLENAR. DETALLES: '), CONSOLE::ERROR);
                                Console::show($e->getMessage(), Console::WARNING);
                                Console::show("Line: " . __LINE__, Console::INFO);
                                Log::add("error", "webscraping", $web, self::trans("HA FALLADO LA CARGA DE DATOS, O BIEN HAY ATRIBUTOS SIN MARCAR O DATOS SIN RELLENAR. DETALLES: ") . '<i class="fa fa-info-circle" title="' . $e->getMessage() . '"></i>');

                                continue;
                            }
                        }

                        for ($i = 0; $i < $docs->length; $i++) {

                            /** @var Element $doc */
                            $doc = $docs[$i];


                            switch ($section['type']) {
                                case 'link':
                                    $data['data'][$i][$section['name']] = trim(preg_replace("/([\r\n]{4,}|[\n]{2,}|[\r]{2,})/", "\n", self::parseUrls($url->getDomain(), $doc->getAttribute('href'))));
                                    //Console::show($data['data'][$i][$section['name']], Console::INFO);

                                    break;
                                case 'text':
                                    $data['data'][$i][$section['name']] = trim(preg_replace("/([\r\n]{4,}|[\n]{2,}|[\r]{2,})/", "\n", $doc->textContent));
                                    //Console::show($data['data'][$i][$section['name']], Console::INFO);

                                    break;
                                case 'image':
                                    $data['data'][$i][$section['name']] = self::getImage($selector, $doc, $url);
                                    //Console::show($data['data'][$i][$section['name']], Console::INFO);
                                    break;
                            }
                        }
                    }
                }

            } catch (\Exception $e) {
                Console::show(self::trans('HA FALLADO LA CARGA DE DATOS, O BIEN HAY ATRIBUTOS SIN MARCAR O DATOS SIN RELLENAR. DETALLES: '), CONSOLE::ERROR);
                Console::show($e->getMessage(), Console::WARNING);
                $process_urls = true;
                continue;
            }
            $processed[] = ["id" => $url->getId(), "data" => $data];
            Console::show(self::trans('Fin procesado url') . ': ' . $web, CONSOLE::SUCCESS);
        }

        foreach ($processed as $key =>  $d) {
            Console::progress($key + (count($urls)), count($urls) * 2);
            /** @var WebscrapingEntity $item */
            $item = $em->getRepository(WebscrapingEntity::class)->findOneById($d['id']);

            $web = 'https://' . $item->getDomain()->getHost() . $item->getPath();

            //$item->setProcessed(true);
            $item->setDateProcessed(new \DateTime);
            $item->setData($d['data']);
            $em->persist($item);
            $log = new ScraperLogsEntity();
            $log->saveLog($d['data'], $d['id'], 'webscraping');
            $em->persist($log);
            Log::add("success", "webscraping", $web, self::trans("URL PROCESADA"));
            Console::show(self::trans('URL PROCESADA') . ': ' . $web, CONSOLE::SUCCESS);

        }

        Console::progress(count($processed), count($processed));
        try {
            $em->flush();
        } catch (\Exception $e) {
            Console::show(self::trans('Error al guardar la información, DETALLES: '), CONSOLE::ERROR);
            Console::show($e->getMessage(), CONSOLE::WARNING);
            $process_urls = true;
        }

        if ($process_urls)
            Console::show(self::trans('Fin del proceso con errores'), CONSOLE::SUCCESS);
        else
            Console::show(self::trans('Fin del proceso'), CONSOLE::SUCCESS);

        Console::show('FIN DEL PROCESADO', Console::INFO);
        Console::show('MEMORIA FINAL: ' . round(memory_get_usage() / 1048576, 2) . "MB", Console::INFO);
    }

    private static function getImage($selector, $doc, $url)
    {

        if (empty($selector['attribute'])) {
            return self::parseUrls($url->getDomain(), $doc->attr('data-src'));
        }

        if ($selector['attribute'] == 'background') {
            if (preg_match('/(?:\([\'"]?)(.*?)(?:[\'"]?\))/', $doc->css("background"), $matches)) {
                return self::parseUrls($url->getDomain(), $matches[1]);
            }
        } elseif ($selector['attribute'] == 'background-image') {
            if (preg_match('/(?:\([\'"]?)(.*?)(?:[\'"]?\))/', $doc->css("background-image"), $matches)) {
                return self::parseUrls($url->getDomain(), $matches[1]);
            }
        } else {
            $str = self::parseUrls($url->getDomain(), $doc->getAttribute($selector['attribute']));

            if (!empty($str)) {
                return self::parseUrls($url->getDomain(), $str);
            }

            $attributes = ['src', 'srcset', 'data-src'];
            foreach ($attributes as $attribute) {
                $str = self::parseUrls($url->getDomain(), $doc->attr($attribute));

                if (!empty($str)) {
                    return self::parseUrls($url->getDomain(), $str);
                }
            }
        }

        return '';

    }

    private static function parseUrls($domain = '', $url = '')
    {
        $url = preg_replace('/(\/asset\/zoomcrop,(\d+),(\d+),(\w+),(\w+))/', '', $url);
        $url = preg_replace('/ \d+w.*/', '', $url);

        if (stripos($url, '//') === 0) {
            $url = 'https:' . $url;
        } elseif (stripos($url, '/') === 0) {
            $url = 'https://' . $domain->getHost() . '/' . substr($url, 1);
        }

        return $url;
    }

    public static function addForm()
    {
        if (self::p("id", 0)) {
            $em = DB::getManager();
            $item = $em->getRepository(WebscrapingEntity::class)->findOneById(self::p("id", 0));
        } else
            $item = null;
        Http::jsonResponse(
            [
                "html" => Smarty::partial(dirname(__FILE__) . '/tpl/form/webscraping-url.tpl', ["item" => $item]),
                "title" => self::trans("Añade una nueva dirección web")
            ]
        );
    }


    /**
     * Devuelve la vista de webscraping
     */
    public static function render(): void
    {

        // Controlde sesion , si no está logado o no tiene permisos redirije
        // a la página principal
        if (!Session::isLogged() || !self::can('view', 'WEBSCRAPING_ACTIONS'))
            Http::redirect(_PATH_);

        // Buscamos el usuario que está logado
        $user = User::$user;
        // Buscamos el cliente
        $client = $user->getClient();
        $show_search_client = false;
        // Si hay cliente asignado ponemos la variable show_search_client a true para mostrar el combo de selección.
        if (!$client) {
            $show_search_client = true;
            // Buscamos la lista de todos los clientes disponibles
            $em = DB::getManager();
            $list_clients = $em->getRepository(ClientEntity::class)->findBy([]);
            Smarty::set("list_clients", $list_clients);
        }
        Smarty::set('page', self::$config['name']);
        Smarty::set('module_dir', dirname(__FILE__));
        Smarty::set('module', self::$config['name']);
        Smarty::set("show_client_search", $show_search_client);


        self::response(Smarty::partial(dirname(__FILE__) . "/tpl/page/webscraping.tpl"), "HTML", 200);
    }


    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Exception
     */
    public static function apiSearch(): void
    {
        $em = DB::getManager();

        //Vamos a buscar que usuario está logado
        //Para saber que cliente tiene asignado
        $user = User::$user;
        $client = $user->getClient();


        /** @var string[] $search */
        $search = self::p('search', ['value' => '']);


        $qb = $em->createQueryBuilder()
            ->select('r')
            ->from(WebscrapingEntity::class, 'r')
            ->innerJoin('r.domain', 'd')
            ->innerJoin('r.template', 't')
            ->setFirstResult(self::p('start', 0))
            ->setMaxResults(self::p('length', 10))
            ->orderBy('r.active', 'desc')
            ->addOrderBy('t.errors', 'desc');


        $total = (new Paginator($qb, true))->count();

        if (!empty($search['value']))
            $qb = $qb->andWhere('d.host LIKE :search')->setParameter('search', '%' . $search['value'] . '%');

        if (self::p('min_date'))
            $qb = $qb->andWhere('r.dateCreated >= :min_date')->setParameter('min_date', new \DateTime(self::p('min_date')));

        if (self::p('max_date'))
            $qb = $qb->andWhere('r.dateCreated <= :max_date')->setParameter('max_date', new \DateTime(self::p('max_date') . '23:59:59'));

        // Si el usuario tiene cliente asigando filtramos por el cliente
        if ($client)
            $qb = $qb->andWhere('r.client = :data')->setParameter('data', $client->getId());
        //Si el usuario no tiene cliente asignado , se entiende que habrá aparecido un combo
        // para indicar que cliente buscar por lo que filtramos por el cliente seleccionado en el combo
        elseif (!empty($search['valueClient']))
            $qb = $qb->andWhere('r.client = :data')->setParameter('data', $search['valueClient']);


        $paginator = new Paginator($qb, true);

        $return = [];
        $return['recordsTotal'] = $total;
        $return['recordsFiltered'] = $paginator->count();

        $return['data'] = [];

        /** @var WebscrapingEntity $post */
        foreach ($paginator as $post) {
            $return['data'][] = [
                "id" => $post->getId(),
                "domain" => $post->getDomain()->getHost(),
                "url" => $post->getPath(),
                'hora_inicio' => $post->getTemplate()->getHoraInicio(),
                'timezone' => $post->getTemplate()->getTimezone(),
                'frecuencia' => $post->getTemplate()->getFrecuencia(),
                'cut_duration' => $post->getTemplate()->getCutDuration(),
                "processed" => $post->getProcessed(),
                "processed_at" => $post->getDateProcessed() ? $post->getDateProcessed()->format(Config::get("params.date.format", DateTimeInterface::W3C)) : false,
                "created_at" => $post->getDateCreated() ? $post->getDateCreated()->format(Config::get("params.date.format", DateTimeInterface::W3C)) : '',
                "active" => $post->getActive(),
                "errors" => $post->getTemplate()->getErrors()
            ];
        }

        self::response($return);
    }

    public static function apiDownloadData($id)
    {
        $item = DB::getManager()->getRepository(WebscrapingEntity::class)->findOneBy(["id" => $id/*, "processed" => true*/]);
        if ($item)
            Http::jsonResponse($item->getData(), 200, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        else
            Http::jsonResponse(["message" => self::trans("Url no procesada")], 401);
    }

    public static function apiProcessed($id)
    {
        if (!User::can("edit", "WEBSCRAPING_ACTIONS")) {
            Display::response(self::trans("Acceso denegado"), "html", 403);
        };

        $em = DB::getManager();
        //@var WebscrapingEntity $item
        $item = $em->getRepository(WebscrapingEntity::class)->findOneById($id);
        $item = $item->setProcessed(false);

        try {
            $em->persist($item);
            $em->flush();
        } catch (\Exception $e) {
            Http::jsonResponse(['message' => $e->getMessage()], 500);
        }

        Http::jsonResponse(['message' => self::trans('Se ha marcado para reenviar correctamente')]);
    }

    public static function apiActive($id)
    {
        if (!User::can("edit", "WEBSCRAPING_ACTIONS")) {
            Display::response(self::trans("Acceso denegado"), "html", 403);
        }

        $em = DB::getManager();
        // @var WebscrapingEntity $item
        $item = $em->getRepository(WebscrapingEntity::class)->findOneById($id);
        $item->setActive(!$item->getActive());


        try {
            $em->persist($item);
            $em->flush();
        } catch (\Exception $e) {
            Http::jsonResponse(['message' => $e->getMessage()], 500);
        }

        if ($item->getActive())
            Http::jsonResponse(['message' => self::trans('Activado correctamente')]);
        else
            Http::jsonResponse(['message' => self::trans('Desactivado correctamente')]);
    }

    public static function apiDelete($id)
    {

        if (!User::can("delete", "WEBSCRAPING_ACTIONS")) {
            Display::response(self::trans("Acceso denegado"), "html", 403);
        }

        $em = DB::getManager();
        $em->getConnection()->beginTransaction();
        try {
            $item = $em
                ->getRepository(WebscrapingEntity::class)
                ->findOneById($id);
            if (!$item)
                Http::jsonResponse(["message" => "El elemento no existe"], 404);
            if (!empty($item->getTemplate())) {
                if (!empty($item->getTemplate()->getLogo()) && file_exists(_WEB_ . $item->getTemplate()->getLogo()))
                    unlink(_WEB_ . $item->getTemplate()->getLogo());
                if (!empty($item->getTemplate()->getDefaultImage()) && file_exists(_WEB_ . $item->getTemplate()->getDefaultImage()))
                    unlink(_WEB_ . $item->getTemplate()->getDefaultImage());
                $item_template = $em->getRepository(WebscrapingTemplateEntity::class)->findOneById($item->getTemplate()->getId());
                $em->remove($item_template);
            }
            $em->remove($item);
            $em->flush();
            $em->getConnection()->commit();
            $return = array("status" => true, "message" => self::trans("Borrado correcto"));
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            $return['error'] = $e->getMessage();
            self::response($return, "JSON");
        }
        self::response($return, "JSON");
    }

    private static function downloadImage($url, $image_url)
    {
        if (stripos($image_url, '//') === 0) {
            $image_url = 'https:' . $image_url;
        } elseif (stripos($image_url, '/') === 0) {

            $image_url = 'https://' . $url->getDomain()->getHost() . $image_url;
        }

        if (stripos($image_url, 'http') === false) {
            return false;
        }

        return $image_url;

        $parsed = parse_url($image_url);
        if (!empty($parsed['query']))
            $image_url = str_replace("?" . $parsed['query'], "", $image_url);

        $source = self::downloadUrl($image_url);

        $directory = _WEB_ . '/c/webscraping-bucle/' . hash("sha256", $url->getId());
        $extension = pathinfo($image_url, PATHINFO_EXTENSION);
        if (!$extension)
            $extension = "jpeg";

        if (!is_dir($directory))
            mkdir($directory, 0777, true);


        $fp = fopen($directory . '/' . md5($image_url) . '.' . $extension, 'w+');
        fwrite($fp, $source);
        fclose($fp);

        return _DOMAIN_ . '/c/webscraping-bucle/' . hash("sha256", $url->getId()) . '/' . md5($image_url) . '.' . $extension;
    }

    private static function downloadUrl($url, $excetion_error = false): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_USERAGENT, Config::get('useragent.firefox_desktop'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        $tmpfname = dirname(__FILE__) . '/cookie.txt';
        curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpfname);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpfname);

        $curlData = curl_exec($ch);

        $curlError = curl_error($ch);


        curl_close($ch);

        //$fp = fopen(dirname(__FILE__) . '/descarga.html', "w+");
        //fwrite($fp, $curlData);
        //fclose($fp);

        if ($curlError && $excetion_error) {
            throw new Exception($curlError);
        }

        if ($curlError) {
            Log::add('error', 'webscraping', $url, self::trans('Url inválida') . ' <i class="fa fa-info-circle" title="' . $curlError . '"></i>');
            return false;
        }
        return $curlData;
    }

    public static function getHtmlPage($id)
    {
        $em = DB::getManager();
        $item = $em->getRepository(WebscrapingEntity::class)->findOneById($id);

        $html = self::downloadUrl('http://' . $item->getDomain()->getHost() . $item->getPath() . $item->getQuery());


        if (empty($html)) {
            Log::add("error", "webscraping", 'http://' . $item->getDomain()->getHost() . $item->getPath(), self::trans("ERROR DE CARGA DE LA URL"));
        }

        $html = str_replace("amp-img", "img", $html);
        $html = str_replace("</img>", "", $html);
        //Http::response($html, 200, 'text/plain');

        // Load CSS FILES
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(utf8_decode($html), LIBXML_NOERROR); // Can replace with $dom->loadHTML($str);

        libxml_use_internal_errors(false);
        $link_tags = $dom->getElementsByTagName('link');

        $css = [];


        $original_css_string = '';

        foreach ($link_tags as $link_tag) {
            $href = $link_tag->getAttribute('href');
            if ($href)
                if (stripos($href, '.css') !== false) {
                    $css[] = $href;
                    //   get href value and load CSS


                    if (preg_match('/^\/\//', $href)) {
                        $href = 'https://' . $href;
                    } else if (preg_match('/^\//', $href)) {
                        $href = 'https://' . $item->getDomain()->getHost() . $href;
                    }

                    //Saltamos los warnings que salgan si no se cargan bién los CSS
                    @$original_css_string .= self::downloadUrl($href);
                }
        }

        $limpieza = ['Could not resolve host: css@'];
        $original_css_string = str_replace($limpieza, "", $original_css_string);

        $original_css_string = str_replace("position:fixed;", "position:initial;", $original_css_string);
        $original_css_string = str_replace("position: fixed;", "position:initial;", $original_css_string);
        $original_css_string = str_replace("position:fixed", "position:initial", $original_css_string);
        $original_css_string = str_replace("position:absolute;", "position:initial;", $original_css_string);
        $original_css_string = str_replace("position: absolute;", "position:initial;", $original_css_string);
        $original_css_string = str_replace(": ##", ": #", $original_css_string);
        $original_css_string = str_replace(": ;", ": '';", $original_css_string);
        $original_css_string = preg_replace("/#\d+x\d+,/", "", $original_css_string);
        $original_css_string = preg_replace("/#\d+x\d+ /", "", $original_css_string);

        $css_string = $original_css_string;

        $parsed = true;

        // 30/11/2021 En Sass clamp() da errores de calculo de unidades
        //$original_css_string = str_replace("clamp(", "calc(clamp(", $original_css_string);
        if (str_contains($css_string, 'clamp(')) {
            $parsed = false;
            var_dump($css_string);
            exit();
        }

        $domains_excluded = ['elpais.com'];

        if ($parsed && !in_array($item->getDomain()->getHost(), $domains_excluded)) {
            try {

                $compiler = new Compiler();
                $css_string = $compiler->compileString('#myIframe { ' . $css_string . ' }')->getCss();

            } catch (ParserException $e) {
                $parsed = false;
            }
        } else
            $parsed = false;

        if ($parsed) {

        }
        if (file_exists(_CACHE_ . '/tmp/webscraping-bucle/' . md5($item->getId()) . '.css'))
            unlink(_CACHE_ . '/tmp/webscraping-bucle/' . md5($item->getId()) . '.css');

        // Guardamos el archivo temporal en temp
        file_put_contents(_CACHE_ . '/tmp/webscraping-bucle/' . md5($item->getId()) . '.css', $parsed ? $css_string : $original_css_string);


        $script = $dom->getElementsByTagName('script');

        $remove = [];
        foreach ($script as $itm) {
            $remove[] = $itm;
        }

        foreach ($remove as $itm) {
            $itm->parentNode->removeChild($itm);
        }

        $imgs = [];
        $attrs = ["src", "data-src", "datasrc", "data-srcset"];

        $img = $dom->getElementsByTagName('img');
        foreach ($img as $itm) {
            $imgs[] = $itm;
        }

        foreach ($imgs as $itm) {
            foreach ($attrs as $attr) {
                $src = $itm->getAttribute($attr);

                if ($src) {
                    $source = self::downloadImage($item, $src);
                    if ($source !== false)
                        $src = $itm->setAttribute("src", self::downloadImage($item, $src));
                }
            }
        }


        $csss = $dom->getElementsByTagName('link');

        $remove = [];
        foreach ($csss as $itm) {
            $remove[] = $itm;
        }

        foreach ($remove as $itm) {
            $itm->parentNode->removeChild($itm);
        }


        $domx = new DOMXPath($dom);
        $items = $domx->query("//*[@onload]");

        foreach ($items as $i) {
            $i->removeAttribute("onload");
        }

        $body = $domx->query('body');

        if ($body && 0 < $body->length) {
            $body = $body->item(0);
            //$body = $dom->savehtml($body);
        }

        $tmp_doc = new DOMDocument();
        $tmp_doc->appendChild($tmp_doc->importNode($body, true));

        return Smarty::partial(dirname(__FILE__) . '/tpl/iframe/page.tpl', [
            "data" => $tmp_doc->saveHTML(),
            "item" => $item,
            "parsed" => $parsed
        ]);
    }

    public static function edit($id): void
    {
        Smarty::set('page', self::$config['name']);
        Smarty::set('module_dir', dirname(__FILE__));
        Smarty::set('module', self::$config['name']);

        $em = DB::getManager();

        $item = $em->getRepository(WebscrapingEntity::class)->findOneById($id);

        if (!$item) {
            Http::response(Smarty::partial("error/error.tpl", ["error" => self::trans("Elemento no encontrado"), "return" => _PATH_ . self::trans(self::$config['name'] . '-slug')]), 404, "text/html");
        }

        $templates = $em->getRepository(WebscrapingTemplateEntity::class)->findByDomain($item->getDomain());

        //Code to get the file...
        //$html = file_get_contents('http://' . $item->getDomain()->getHost() . $item->getPath());

        //Buscamos el usuario logado actualmente
        $user = User::$user;

        if ($user->getType()->getReference() == 'Client') {
            //Si es un usuario tipo cliente (En la tabla Client tiene valor 1)
            $usuario_cliente = true;
            $clientes = [];
        } else {
            $clientes = $em->getRepository(ClientEntity::class)->findBy([], ['name' => 'ASC']);
            $usuario_cliente = false;
        }
        if ($item->getTemplate()) {
            $works = $em->getRepository(VideoProducerWorkEntity::class)->findBy(['template' => $item->getTemplate()->getId()], ['id' => 'desc']);
            foreach ($works as $videoProducerWork) {
                if (!empty($videoProducerWork->getCdnUrl())) {
                    $item->getTemplate()->setVideoproducer($videoProducerWork);
                    break;
                }
            }
            if (!empty($item->getTemplate()->getLogo())) {
                $item->getTemplate()->setLogoImagen();
            }
            if (!empty($item->getTemplate()->getDefaultImage())) {
                $item->getTemplate()->setDefaultImageImagen();
            }
        }

        Http::response(
            Smarty::partial(
                dirname(__FILE__) . '/tpl/form/webscraping.tpl',
                [
                    'item' => $item,
                    'data' => self::getHtmlPage($item->getId()),
                    'css_files' => [],
                    'parsed' => true,
                    'templates' => $templates,
                    'clientes' => $clientes,
                    'usuario_cliente' => $usuario_cliente
                ]
            ),
            200,
            'html'
        );
    }

    public static function apiEdit($id)
    {
        /** @var WebScrapingEntity $item */
        $item = DB::getManager()->getRepository(WebscrapingEntity::class)->findOneById($id);

        if (empty($item)) {
            Http::jsonResponse(['message' => 'Not found'], 404);
        }

        Http::jsonResponse(
            [
                'id' => $item->getId(),
                'domain' => $item->getDomain()->getHost(),
                'path' => $item->getPath(),
                //'query' => $item->getQuery(),
                'date_processed' => $item->getDateProcessed() ? $item->getDateProcessed()->format("Y-m-d\TH:i:s") : null,
                'date_created' => $item->getDateCreated() ? $item->getDateCreated()->format("Y-m-d\TH:i:s") : null,
                'date_modified' => $item->getDateModified() ? $item->getDateModified()->format("Y-m-d\TH:i:s") : null,

            ],
            200
        );
    }

    public static function apiParseUrl()
    {
        $url = self::p("url", false);


        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            Http::jsonResponse([
                'message' => self::trans('Error al validar la url'),
            ], 400);
        }

        //Buscamos el usuario logado actualmente
        $user = User::$user;

        // Sacamos el dominio
        $data = parse_url($url);

        $em = DB::getManager();

        $domain = $em->getRepository(DomainEntity::class)->findOneBy(['host' => $data['host']]);
        if (!$domain) {
            $domain = new DomainEntity();
            $domain->setHost($data['host']);
            $domain->setActive(true);
            $domain->setDateCreated(new \DateTime());
            $domain->setDateModified(new \DateTime());
            $em->persist($domain);
        }

        /** @var WebscrapingEntity $item */
        $item = $em->getRepository(WebscrapingEntity::class)->findOneBy([
            'domain' => $domain,
            'path' => $data['path'] ?? '/'
        ]);

        // Si existe la url soltamos un error indicando la id de la url existente
        if ($item) {
            Http::jsonResponse([
                'message' => self::trans('La url ya está registrada'),
                'info' => $item->getId(),
                'url' => $data
            ], 409);
        }

        $item = new WebscrapingEntity();
        $item->setActive(1);
        $item->setDomain($domain);
        $item->setPath($data['path'] ?? '/');
        $item->setQuery($data['query'] ?? '');
        $item->setDateCreated(new \DateTime());
        $item->setDateModified(new \DateTime());
        //Le pasamos el cliente del usuario logado
        $item->setClient($user->getClient());

        $template = new WebscrapingTemplateEntity();
        $template->setDateCreated(new \DateTime());
        $template->setDateModified(new \DateTime());
        $template->setDomain($domain);
        $template->setActive(true);
        $template->setData([]);
        $template->setIgnored([]);
        $template->setName($domain->getHost());
        $template->setTitle('');
        $template->setDescription('');
        $template->setHoraInicio('00:00');
        $template->setTimezone('Europe/Madrid');
        $template->setFrecuencia('12:00:00');
        $template->setCutDuration('00:00:05');
        $template->setClaim('');
        $template->setColor('#000000');
        $template->setAudio(false);
        $template->setLogo('');
        $template->setUseDefaultImage(false);
        $template->setDefaultImage('');
        $em->persist($template);
        $item->setTemplate($template);

        try {
            $em->persist($item);
            $em->flush();
        } catch (\Exception $e) {
            // Error response
            Http::jsonResponse([
                'message' => self::trans('Error al guardar los datos'),
                'extraMessage' => $e->getMessage()
            ], 500);
        }

        // Response
        Http::jsonResponse([
            'message' => self::trans('Url guardada con éxito'),
            'info' => $item->getId(),
            'url' => $data
        ]);
    }

    public static function apiGetTemplate($id)
    {
        $em = DB::getManager();

        $template = $em->getRepository(WebscrapingTemplateEntity::class)->findOneById($id);
        if (!$template) {
            Http::jsonResponse(["message" => self::trans("Generando una nueva plantilla")], 404);
        }
        if (!empty($template->getLogo())) {
            $template->setLogoImagen();
        }
        if (!empty($template->getDefaultImage())) {
            $template->setDefaultImageImagen();
        }
        Http::jsonResponse([
            "id" => $template->getId(),
            "name" => $template->getName(),
            "data" => $template->getData(),
            "title" => $template->getTitle(),
            "description" => $template->getDescription(),
            "horaInicio" => $template->getHoraInicio(),
            "timezone" => $template->getTimezone(),
            "frecuencia" => $template->getFrecuencia(),
            "cutDuration" => $template->getCutDuration(),
            "claim" => $template->getClaim(),
            "color" => $template->getColor(),
            "audio" => $template->getAudio(),
            "logo" => $template->getLogo(),
            "logoImagen" => $template->getLogoImagen(),
            "useDefaultImage" => $template->getUseDefaultImage(),
            "defaultImage" => $template->getDefaultImage(),
            "defaultImageImagen" => $template->getDefaultImageImagen(),
            "ignored" => $template->getIgnored()
        ], 200);
    }

    public static function apiSaveTemplate()
    {
        $em = DB::getManager();
        $json = json_decode(file_get_contents('php://input'), true);
        $request = $json['template'] ?? [];


        $item = $em->getRepository(WebscrapingEntity::class)->findOneById($json['item']);
        $template = $em->getRepository(WebscrapingTemplateEntity::class)->findOneById($request['id']);


        // Si nos han pasado una id de cliente recuperamos el cliente , si no cargamos el ya existente
        $client = (!empty($request['client'])) ? $em->getRepository(ClientEntity::class)->findOneById($request['client']) : $item->getClient();
        if (!$client) {
            Http::jsonResponse(["message" => "Es obligatorio seleccionar un cliente"], 400);
        }

        foreach ($request['sections'] ?? [] as &$section) {
            if (empty($section['identifier']))
                $section['identifier'] = [];

            $section['required'] = (bool)filter_var($section['required'], FILTER_VALIDATE_BOOLEAN);
        }

        if (!$template) {
            $template = new WebscrapingTemplateEntity();

            $template->setDateCreated(new \DateTime());
        }

        $template->setName($request['name']);
        $template->setData(!empty($request['sections']) ? $request['sections'] : []);
        $template->setIgnored(!empty($request['ignored']) ? $request['ignored'] : []);
        $template->setActive(true);
        $template->setTitle($request['title']);
        $template->setDescription($request['description']);
        $template->setHoraInicio($request['horaInicio']);
        $template->setTimezone($request['timezone']);
        $template->setFrecuencia($request['frecuencia']);
        $template->setCutDuration($request['cutDuration']);
        $template->setClaim($request['claim']);
        $template->setColor($request['color']);
        $template->setAudio($request['audio']);

        // Save defaultImage
        $image_configuration = [
            $request['logoRuta'] ?? null,
            $request['logo'] ?? null,
            $template->getId(),
            $template->getLogo(),
            '/logos',
            self::URL_LOGOS
        ];

        try {
            if (!empty($request['logo'])) {
                $template->setLogo(self::saveImage($image_configuration));
            }
        } catch (Exception $e) {
            Http::jsonResponse(["message" => "Error al guardar la imagen"], 400);
        }

        $template->setUseDefaultImage($request['useDefaultImage']);
        // Save defaultImage
        $image_configuration = [
            $request['defaultImageRuta'] ?? null,
            $request['defaultImage'] ?? null,
            $template->getId(),
            $template->getDefaultImage(),
            '/defaultImages',
            self::URL_DEFAULT_IMAGES
        ];

        try {
            if (!empty($request['defaultImage'])) {
                $template->setDefaultImage(self::saveImage($image_configuration));
            }
        } catch (Exception $e) {
            Http::jsonResponse(["message" => "Error al guardar la imagen"], 400);
        }

        $template->setDateModified(new \DateTime());
        $old_client = $template->getCLient();
        if ($old_client) {
            $old_client->removeDomain($template->getDomain());

            $em->persist($old_client);
        }
        $template->setDomain($item->getDomain());
        $template->setClient($client);

        $em->persist($template);
        $item->setTemplate($template);
        $item->setClient($client);

        $em->persist($item);
        $client->addDomain($item->getDomain());
        $domain = $item->getDomain();
        if ($old_client)
            $domain->removeClient($old_client);

        $domainClient = $domain->getClients()->filter(
            function ($c) use ($client) {
                return ($c->getId() === $client->getId());
            }
        );
        if (!$domainClient)
            $domain->addClient($client);

        $em->persist($domain);
        $em->persist($client);
        try {

            $em->flush();
        } catch (\Exception $e) {
            Http::jsonResponse(['message' => $e->getMessage()], 500);
        }

        if (!empty($template->getLogo())) {
            $template->setLogoImagen();
        }
        if (!empty($template->getDefaultImage())) {
            $template->setDefaultImageImagen();
        }


        Http::jsonResponse([
            "id" => $template->getId(),
            "name" => $template->getName(),
            "data" => $template->getData(),
            "title" => $template->getTitle(),
            "description" => $template->getDescription(),
            "horaInicio" => $template->getHoraInicio(),
            "timezone" => $template->getTimezone(),
            "frecuencia" => $template->getFrecuencia(),
            "cutDuration" => $template->getCutDuration(),
            "claim" => $template->getClaim(),
            "color" => $template->getColor(),
            "audio" => $template->getAudio(),
            "logo" => $template->getLogo(),
            "logoImagen" => $template->getLogoImagen(),
            "useDefaultImage" => $template->getUseDefaultImage(),
            "defaultImage" => $template->getDefaultImage(),
            "defaultImageImagen" => $template->getDefaultImageImagen(),
            "ignore" => $template->getIgnored(),
            "message" => self::trans('Guardado correctamente')
        ], 200);
    }

    public static function saveImage(array $image_configuration)
    {
        list($defaultImageRoute, $defaultImage, $template_image_id, $template_image, $main_route, $sub_route) = $image_configuration;
        if (!empty($defaultImageRoute)) {
            return $defaultImageRoute;
        }

        if (empty($defaultImage)) {
            return null;
        }

        $pattern = '/data:image\/(.+);base64,(.*)/m';

        self::createFolders($main_route, $sub_route);

        if ($defaultImage == 'borrar') {
            if (!empty($template_image) && preg_match($pattern, $template_image, $matches)) {
                $dir_image = _WEB_ . $sub_route . md5($template_image_id) . '.' . $matches[1];
                if (!empty($matches[1]) && file_exists($dir_image)) {
                    unlink($dir_image);
                }
            }
            return null;
        }

        $image_explode = explode(',', $defaultImage);
        $image = $image_explode[1] ?? '';
        $extension = '';

        if (preg_match('/data:image\/(?<extension>.+);base64/', $image_explode[0], $matches)) {
            $extension = $matches['extension'];
        }

        if (!empty($extension) && !empty($image)) {
            $path = $sub_route . md5($template_image_id) . '.' . $extension;
            file_put_contents(_WEB_ . $path, base64_decode($image));
            return $path;
        }

        throw new Exception('Error save image');
    }

    private static function createFolders($base_folder, $type_folder)
    {
        if (!is_dir(_WEB_ . $base_folder)) {
            mkdir(_WEB_ . $base_folder, 0777, true);
        }
        if (!is_dir(_WEB_ . $type_folder)) {
            mkdir(_WEB_ . $type_folder, 0777, true);
        }
    }
}
