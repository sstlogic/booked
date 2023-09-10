<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Booked-SessionToken, X-Booked-UserId');
header('Vary: Origin');
header('Access-Control-Max-Age: 30');
header('Content-Type: application/javascript');

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Routing\RouteContext;
use Slim\Factory\AppFactory;
use Psr\Http\Server\MiddlewareInterface;

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Presenters/ApiDtos/ApiHelperFunctions.php');
require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'lib/WebService/Slim/namespace.php');
require_once(ROOT_DIR . 'vendor/autoload.php');

require_once(ROOT_DIR . 'WebServices/AuthenticationWebService.php');
require_once(ROOT_DIR . 'WebServices/ReservationsWebService.php');
require_once(ROOT_DIR . 'WebServices/ReservationWriteWebService.php');
require_once(ROOT_DIR . 'WebServices/ResourcesWebService.php');
require_once(ROOT_DIR . 'WebServices/ResourcesWriteWebService.php');
require_once(ROOT_DIR . 'WebServices/UsersWebService.php');
require_once(ROOT_DIR . 'WebServices/UsersWriteWebService.php');
require_once(ROOT_DIR . 'WebServices/SchedulesWebService.php');
require_once(ROOT_DIR . 'WebServices/AttributesWebService.php');
require_once(ROOT_DIR . 'WebServices/AttributesWriteWebService.php');
require_once(ROOT_DIR . 'WebServices/GroupsWebService.php');
require_once(ROOT_DIR . 'WebServices/GroupsWriteWebService.php');
require_once(ROOT_DIR . 'WebServices/AccessoriesWebService.php');
require_once(ROOT_DIR . 'WebServices/AccountWebService.php');
require_once(ROOT_DIR . 'Web/Services/Help/ApiHelpPage.php');
require_once(ROOT_DIR . 'lib/Common/Logging/Log.php');

if (!Configuration::Instance()->GetSectionKey(ConfigSection::API, ConfigKeys::API_ENABLED, new BooleanConverter())) {
    die("Booked Scheduler API has been configured as disabled.<br/><br/>Set \$conf['settings']['api']['enabled'] = 'true' to enable.");
}

if (BookedStringHelper::Contains($_SERVER["REQUEST_URI"], 'index.php')) {
    $headers = getallheaders();
    $contentType = $headers['Content-Type'];
    $newUrl = Configuration::Instance()->GetScriptUrl() . '/Services';
    $error = "Please update your service calls to use a base url of $newUrl";
    if (!empty($contentType) && (BookedStringHelper::Contains($contentType, "json",) || BookedStringHelper::Contains($contentType, "javascript"))) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode(['error' => $error]);
    } else {
        echo $error;
    }

    die();
}

class SecurityMiddleware
{
    private $app;
    private $server;
    private $registry;

    public function __construct($app, $server, $registry)
    {
        $this->app = $app;
        $this->server = $server;
        $this->registry = $registry;
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeName = $route->getName();
        if ($this->registry->IsSecure($routeName)) {
            $security = new WebServiceSecurity(new UserSessionRepository());
            $wasHandled = $security->HandleSecureRequest($request, $this->server, $this->registry->IsLimitedToAdmin($routeName));
            if (!$wasHandled) {
                $response = new Slim\Psr7\Response();
                $response = $response->withStatus(RestResponse::UNAUTHORIZED_CODE);
                $response->getBody()->write(json_encode(['error' => 'You must be authenticated in order to access this service.',
                    'href' => $this->server->GetFullServiceUrl(WebServices::Login)]));
                return $response;
            } else {
                return $handler->handle($request);
            }
        }

        return $handler->handle($request);
    }
}

class TrailingSlashMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        Log::Debug('trailing slash', ['uri' => $uri, 'path' => $path]);

        if ($path != '' && $path != 'index.php' && $path != '/' && substr($path, -1) == '/' && !BookedStringHelper::EndsWith($path, "/Web/Services/")) {
            $path = rtrim($path, '/');

            $uri = $uri->withPath($path);

            if ($request->getMethod() == 'GET') {
                $response = new Slim\Psr7\Response();
                return $response
                    ->withHeader('Location', (string)$uri)
                    ->withStatus(301);
            } else {
                $request = $request->withUri($uri);
            }
        }

        return $handler->handle($request);
    }
}

$app = AppFactory::create();
$server = new SlimServer($app);
$registry = new SlimWebServiceRegistry($app);
//$app->add(new TrailingSlashMiddleware());
$app->add(new SecurityMiddleware($app, $server, $registry));
$app->addRoutingMiddleware();

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable              $exception,
    bool                   $displayErrorDetails,
    bool                   $logErrors,
    bool                   $logErrorDetails,
    ?LoggerInterface       $logger = null
) use ($app) {
    Log::Error('Slim Exception.', ['message' => $exception->getMessage(), 'exception' => $exception->getTraceAsString()]);

    $response = $app->getResponseFactory()->createResponse();
    $response->withStatus(RestResponse::SERVER_ERROR);
    $response->withHeader('Content-Type', "application/json");
    $response->getBody()->write(json_encode('Exception was logged.'));

    return $response;
};

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

define('BASE_URL', dirname($_SERVER['SCRIPT_NAME']));

RegisterHelp($registry, $app);
RegisterAuthentication($server, $registry);
RegisterReservations($server, $registry);
RegisterResources($server, $registry);
RegisterUsers($server, $registry);
RegisterSchedules($server, $registry);
RegisterAttributes($server, $registry);
RegisterGroups($server, $registry);
RegisterAccessories($server, $registry);
RegisterAccounts($server, $registry);

$app->run();

function RegisterHelp(SlimWebServiceRegistry $registry, \Slim\App $app)
{
    $app->get(BASE_URL . "/", function (Request $request, Response $response) use ($registry, $app) {
        // Print API documentation

        $newResponse = $response->withHeader('Content-Type', 'text/html');
        $newResponse->getBody()->write(ApiHelpPage::Render($registry, $app));
        return $newResponse;
    });
}

function RegisterAuthentication(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $webService = new AuthenticationWebService($server,
        new WebServiceAuthentication(
            PluginManager::Instance()->LoadAuthentication(),
            new UserSessionRepository(),
            new WebServiceApiUserAuthentication(PluginManager::Instance()->LoadAuthorization(),
                new UserRepository(),
                new GroupRepository())
        )
    );
    $category = new SlimWebServiceRegistryCategory('Authentication', BASE_URL);
    $category->AddSecurePost('SignOut', [$webService, 'SignOut'], WebServices::Logout);
    $category->AddPost('Authenticate', [$webService, 'Authenticate'], WebServices::Login);
    $registry->AddCategory($category);
}

function RegisterReservations(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $readService = new ReservationsWebService($server, new ReservationViewRepository(), new PrivacyFilter(new ReservationAuthorization(PluginManager::Instance()->LoadAuthorization())), new AttributeService(new AttributeRepository()));
    $writeService = new ReservationWriteWebService($server, new ReservationSaveController(ReservationApiPresenter::Create()));

    $category = new SlimWebServiceRegistryCategory('Reservations', BASE_URL);
    $category->AddSecurePost('/', [$writeService, 'Create'], WebServices::CreateReservation);
    $category->AddSecurePost('', [$writeService, 'Create'], WebServices::CreateReservation);
    $category->AddSecureGet('/', [$readService, 'GetReservations'], WebServices::AllReservations);
    $category->AddSecureGet('', [$readService, 'GetReservations'], WebServices::AllReservations);
    $category->AddSecureGet('/{referenceNumber}', [$readService, 'GetReservation'], WebServices::GetReservation);
    $category->AddSecurePost('/{referenceNumber}', [$writeService, 'Update'], WebServices::UpdateReservation);
    $category->AddSecurePost('/{referenceNumber}/Approval', [$writeService, 'Approve'], WebServices::ApproveReservation);
    $category->AddSecurePost('/{referenceNumber}/CheckIn', [$writeService, 'Checkin'], WebServices::CheckinReservation);
    $category->AddSecurePost('/{referenceNumber}/CheckOut', [$writeService, 'Checkout'], WebServices::CheckoutReservation);
    $category->AddSecureDelete('/{referenceNumber}', [$writeService, 'Delete'], WebServices::DeleteReservation);

    $registry->AddCategory($category);
}

function RegisterResources(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $resourceRepository = new ResourceRepository();
    $attributeService = new AttributeService(new AttributeRepository());
    $webService = new ResourcesWebService($server, $resourceRepository, $attributeService, new ReservationViewRepository());
    $writeWebService = new ResourcesWriteWebService($server, new ResourceSaveController($resourceRepository, new ResourceRequestValidator($attributeService)));
    $category = new SlimWebServiceRegistryCategory('Resources', BASE_URL);
    $category->AddGet('/Status', [$webService, 'GetStatuses'], WebServices::GetStatuses);
    $category->AddSecureGet('', [$webService, 'GetAll'], WebServices::AllResources);
    $category->AddSecureGet('/', [$webService, 'GetAll'], WebServices::AllResources);
    $category->AddSecureGet('/Status/Reasons', [$webService, 'GetStatusReasons'], WebServices::GetStatusReasons);
    $category->AddSecureGet('/Availability', [$webService, 'GetAvailability'], WebServices::AllAvailability);
    $category->AddSecureGet('/Groups', [$webService, 'GetGroups'], WebServices::GetResourceGroups);
    $category->AddSecureGet('/Types', [$webService, 'GetTypes'], WebServices::GetResourceTypes);
    $category->AddSecureGet('/{resourceId}', [$webService, 'GetResource'], WebServices::GetResource);
    $category->AddSecureGet('/{resourceId}/Availability', [$webService, 'GetAvailability'], WebServices::GetResourceAvailability);
    $category->AddAdminPost('/', [$writeWebService, 'Create'], WebServices::CreateResource);
    $category->AddAdminPost('/{resourceId}', [$writeWebService, 'Update'], WebServices::UpdateResource);
    $category->AddAdminDelete('/{resourceId}', [$writeWebService, 'Delete'], WebServices::DeleteResource);
    $registry->AddCategory($category);
}

function RegisterAccessories(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $webService = new AccessoriesWebService($server, new ResourceRepository(), new AccessoryRepository());
    $category = new SlimWebServiceRegistryCategory('Accessories', BASE_URL);
    $category->AddSecureGet('/', [$webService, 'GetAll'], WebServices::AllAccessories);
    $category->AddSecureGet('', [$webService, 'GetAll'], WebServices::AllAccessories);
    $category->AddSecureGet('/{accessoryId}', [$webService, 'GetAccessory'], WebServices::GetAccessory);
    $registry->AddCategory($category);
}

function RegisterUsers(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $attributeService = new AttributeService(new AttributeRepository());
    $webService = new UsersWebService($server, new UserRepositoryFactory(), $attributeService);
    $writeWebService = new UsersWriteWebService($server,
        new UserSaveController(new ManageUsersServiceFactory(), new UserRequestValidator($attributeService, new UserRepository())));
    $category = new SlimWebServiceRegistryCategory('Users', BASE_URL);
    $category->AddSecureGet('/', [$webService, 'GetUsers'], WebServices::AllUsers);
    $category->AddSecureGet('', [$webService, 'GetUsers'], WebServices::AllUsers);
    $category->AddGet('/Languages', [$webService, 'GetLanguages'], WebServices::Languages);
    $category->AddGet('/PhoneCountryCodes', [$webService, 'GetPhoneCountryCodes'], WebServices::PhoneCountryCodes);
    $category->AddSecureGet('/{userId}', [$webService, 'GetUser'], WebServices::GetUser);
    $category->AddAdminPost('/', [$writeWebService, 'Create'], WebServices::CreateUser);
    $category->AddAdminPost('', [$writeWebService, 'Create'], WebServices::CreateUser);
    $category->AddAdminPost('/{userId}', [$writeWebService, 'Update'], WebServices::UpdateUser);
    $category->AddAdminPost('/{userId}/Status', [$writeWebService, 'UpdateStatus'], WebServices::UpdateUserStatus);
    $category->AddAdminPost('/{userId}/Password', [$writeWebService, 'UpdatePassword'], WebServices::UpdatePassword);
    $category->AddAdminDelete('/{userId}', [$writeWebService, 'Delete'], WebServices::DeleteUser);
    $registry->AddCategory($category);
}

function RegisterSchedules(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $webService = new SchedulesWebService($server, new ScheduleRepository(), new PrivacyFilter(new ReservationAuthorization(PluginManager::Instance()->LoadAuthorization())));
    $category = new SlimWebServiceRegistryCategory('Schedules', BASE_URL);
    $category->AddSecureGet('/', [$webService, 'GetSchedules'], WebServices::AllSchedules);
    $category->AddSecureGet('', [$webService, 'GetSchedules'], WebServices::AllSchedules);
    $category->AddSecureGet('/{scheduleId}', [$webService, 'GetSchedule'], WebServices::GetSchedule);
    $category->AddSecureGet('/{scheduleId}/Slots', [$webService, 'GetSlots'], WebServices::GetScheduleSlots);
    $registry->AddCategory($category);
}

function RegisterAttributes(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $webService = new AttributesWebService($server, new AttributeService(new AttributeRepository()));
    $writeWebService = new AttributesWriteWebService($server, new AttributeSaveController(new AttributeRepository()));

    $category = new SlimWebServiceRegistryCategory('Attributes', BASE_URL);
    $category->AddSecureGet('Category/{categoryId}', [$webService, 'GetAttributes'], WebServices::AllCustomAttributes);
    $category->AddSecureGet('/{attributeId}', [$webService, 'GetAttribute'], WebServices::GetCustomAttribute);
    $category->AddAdminPost('/', [$writeWebService, 'Create'], WebServices::CreateCustomAttribute);
    $category->AddAdminPost('', [$writeWebService, 'Create'], WebServices::CreateCustomAttribute);
    $category->AddAdminPost('/{attributeId}', [$writeWebService, 'Update'], WebServices::UpdateCustomAttribute);
    $category->AddAdminDelete('/{attributeId}', [$writeWebService, 'Delete'], WebServices::DeleteCustomAttribute);
    $registry->AddCategory($category);
}

function RegisterGroups(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $groupRepository = new GroupRepository();
    $webService = new GroupsWebService($server, $groupRepository, $groupRepository);
    $writeWebService = new GroupsWriteWebService($server, new GroupSaveController($groupRepository, new ResourceRepository(), new ScheduleRepository()));

    $category = new SlimWebServiceRegistryCategory('Groups', BASE_URL);

    $category->AddSecureGet('/', [$webService, 'GetGroups'], WebServices::AllGroups);
    $category->AddSecureGet('', [$webService, 'GetGroups'], WebServices::AllGroups);
    $category->AddSecureGet('/{groupId}', [$webService, 'GetGroup'], WebServices::GetGroup);
    $category->AddAdminPost('/', [$writeWebService, 'Create'], WebServices::CreateGroup);
    $category->AddAdminPost('', [$writeWebService, 'Create'], WebServices::CreateGroup);
    $category->AddAdminPost('/{groupId}', [$writeWebService, 'Update'], WebServices::UpdateGroup);
    $category->AddAdminPost('/{groupId}/Roles', [$writeWebService, 'Roles'], WebServices::UpdateGroupRoles);
    $category->AddAdminPost('/{groupId}/Permissions', [$writeWebService, 'Permissions'], WebServices::UpdateGroupPermissions);
    $category->AddAdminPost('/{groupId}/Users', [$writeWebService, 'Users'], WebServices::UpdateGroupUsers);
    $category->AddAdminDelete('/{groupId}', [$writeWebService, 'Delete'], WebServices::DeleteGroup);

    $registry->AddCategory($category);
}

function RegisterAccounts(SlimServer $server, SlimWebServiceRegistry $registry)
{
    $userRepository = new UserRepository();
    $attributeService = new AttributeService(new AttributeRepository());
    $password = new Password();
    $registration = new Registration($password, $userRepository, new RegistrationNotificationStrategy(), new RegistrationPermissionStrategy(), new GroupRepository());
    $controller = new AccountController($registration, $userRepository, new AccountRequestValidator($attributeService, $userRepository), $password, $attributeService);

    $webService = new AccountWebService($server, $controller);

    $category = new SlimWebServiceRegistryCategory('Accounts', BASE_URL);
    $category->AddPost('/Registration', [$webService, 'Create'], WebServices::CreateAccount);
    $category->AddSecurePost('/', [$webService, 'Update'], WebServices::UpdateAccount);
    $category->AddSecurePost('', [$webService, 'Update'], WebServices::UpdateAccount);
    $category->AddSecurePost('/Password', [$webService, 'UpdatePassword'], WebServices::UpdateAccountPassword);
    $category->AddSecureGet('/', [$webService, 'GetAccount'], WebServices::GetAccount);
    $category->AddSecureGet('', [$webService, 'GetAccount'], WebServices::GetAccount);

    $registry->AddCategory($category);
}
