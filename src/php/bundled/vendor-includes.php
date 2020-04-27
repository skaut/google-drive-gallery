<?php
/**
 * Includes all the PHP dependencies from the "fake vendor" directory
 *
 * As the plugin contains a "fake" vendor directory where dependecies are selectively copied to, the vendor autoloading doesn't work. This file manually includes all the dependencies.
 *
 * @package skaut-google-drive-gallery
 */

// Do not change the order of these, they might break.
require_once __DIR__ . '/vendor/psr/http-message/src/UriInterface.php';
require_once __DIR__ . '/vendor/psr/http-message/src/MessageInterface.php';
require_once __DIR__ . '/vendor/psr/http-message/src/RequestInterface.php';
require_once __DIR__ . '/vendor/psr/http-message/src/StreamInterface.php';
require_once __DIR__ . '/vendor/psr/http-message/src/ResponseInterface.php';

require_once __DIR__ . '/vendor/psr/log/Psr/Log/LoggerInterface.php';

require_once __DIR__ . '/vendor/psr/cache/src/CacheItemPoolInterface.php';
require_once __DIR__ . '/vendor/psr/cache/src/CacheItemInterface.php';

require_once __DIR__ . '/vendor/symfony/polyfill-intl-idn/Idn.php';
require_once __DIR__ . '/vendor/symfony/polyfill-intl-idn/bootstrap.php';

require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/GuzzleException.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/TransferException.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/RequestException.php';

require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/StreamHandler.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/CurlHandler.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/Proxy.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Handler/EasyHandle.php';

require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/functions.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/RequestOptions.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/RedirectMiddleware.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Middleware.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/ClientInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Client.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/HandlerStack.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Utils.php';

require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/functions.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Stream.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/MessageTrait.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Request.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Uri.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/UriResolver.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Response.php';

require_once __DIR__ . '/vendor/guzzlehttp/promises/src/functions.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/PromiseInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/Promise.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/TaskQueueInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/TaskQueue.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/FulfilledPromise.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/RejectedPromise.php';

require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/ResettableInterface.php';
require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Logger.php';

require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Handler/HandlerInterface.php';
require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Handler/AbstractHandler.php';
require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Handler/AbstractProcessingHandler.php';
require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Handler/StreamHandler.php';

require_once __DIR__ . '/vendor/google/auth/src/Cache/MemoryCacheItemPool.php';
require_once __DIR__ . '/vendor/google/auth/src/Cache/Item.php';

require_once __DIR__ . '/vendor/google/auth/src/HttpHandler/Guzzle6HttpHandler.php';
require_once __DIR__ . '/vendor/google/auth/src/HttpHandler/HttpHandlerFactory.php';

require_once __DIR__ . '/vendor/google/auth/src/CacheTrait.php';
require_once __DIR__ . '/vendor/google/auth/src/FetchAuthTokenInterface.php';
require_once __DIR__ . '/vendor/google/auth/src/OAuth2.php';

require_once __DIR__ . '/vendor/google/auth/src/Middleware/ScopedAccessTokenMiddleware.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/Http/Batch.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/Http/REST.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/Task/Runner.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/AccessToken/Revoke.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/AuthHandler/AuthHandlerFactory.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/AuthHandler/Guzzle6AuthHandler.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/Exception.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/Service/Resource.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/Service/Exception.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/Utils/UriTemplate.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Google/Client.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/Model.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/Collection.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Google/Service.php';

require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Drive.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveList.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileImageMediaMetadata.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveFile.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/FileList.php';

require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/About.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Changes.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Channels.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Comments.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Drives.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Files.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Permissions.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Replies.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Revisions.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Resource/Teamdrives.php';

require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive.php';
