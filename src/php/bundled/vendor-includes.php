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
require_once __DIR__ . '/vendor/symfony/polyfill-mbstring/Mbstring.php';
require_once __DIR__ . '/vendor/symfony/polyfill-mbstring/bootstrap.php';

require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Cookie/SetCookie.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Cookie/CookieJar.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/GuzzleException.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/TransferException.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/RequestException.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/BadResponseException.php';
require_once __DIR__ . '/vendor/guzzlehttp/guzzle/src/Exception/ClientException.php';

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
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Query.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Request.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Uri.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/UriResolver.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Utils.php';
require_once __DIR__ . '/vendor/guzzlehttp/psr7/src/Response.php';

require_once __DIR__ . '/vendor/guzzlehttp/promises/src/functions.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/Create.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/Each.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/PromisorInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/EachPromise.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/Is.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/PromiseInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/Promise.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/TaskQueueInterface.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/TaskQueue.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/FulfilledPromise.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/RejectedPromise.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/RejectionException.php';
require_once __DIR__ . '/vendor/guzzlehttp/promises/src/Utils.php';

require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/ResettableInterface.php';
require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Logger.php';
require_once __DIR__ . '/vendor/monolog/monolog/src/Monolog/Utils.php';

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
require_once __DIR__ . '/vendor/google/auth/src/GetQuotaProjectInterface.php';
require_once __DIR__ . '/vendor/google/auth/src/SignBlobInterface.php';
require_once __DIR__ . '/vendor/google/auth/src/ProjectIdProviderInterface.php';
require_once __DIR__ . '/vendor/google/auth/src/UpdateMetadataInterface.php';
require_once __DIR__ . '/vendor/google/auth/src/FetchAuthTokenCache.php';
require_once __DIR__ . '/vendor/google/auth/src/CredentialsLoader.php';

require_once __DIR__ . '/vendor/google/auth/src/Middleware/AuthTokenMiddleware.php';
require_once __DIR__ . '/vendor/google/auth/src/Middleware/ScopedAccessTokenMiddleware.php';

require_once __DIR__ . '/vendor/google/auth/src/OAuth2.php';

require_once __DIR__ . '/vendor/google/auth/src/Credentials/UserRefreshCredentials.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Exception.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Http/Batch.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Http/MediaFileUpload.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Http/REST.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Task/Exception.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Task/Composer.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Task/Retryable.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Task/Runner.php';

require_once __DIR__ . '/vendor/google/apiclient/src/AccessToken/Revoke.php';
require_once __DIR__ . '/vendor/google/apiclient/src/AccessToken/Verify.php';

require_once __DIR__ . '/vendor/google/apiclient/src/AuthHandler/AuthHandlerFactory.php';
require_once __DIR__ . '/vendor/google/apiclient/src/AuthHandler/Guzzle5AuthHandler.php';
require_once __DIR__ . '/vendor/google/apiclient/src/AuthHandler/Guzzle6AuthHandler.php';
require_once __DIR__ . '/vendor/google/apiclient/src/AuthHandler/Guzzle7AuthHandler.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Service/Resource.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Service/Exception.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Utils/UriTemplate.php';

require_once __DIR__ . '/vendor/google/apiclient/src/Client.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Model.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Collection.php';
require_once __DIR__ . '/vendor/google/apiclient/src/Service.php';
require_once __DIR__ . '/vendor/google/apiclient/src/aliases.php';

require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/Drive.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveList.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileShortcutDetails.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileImageMediaMetadata.php';
require_once __DIR__ . '/vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileVideoMediaMetadata.php';
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
