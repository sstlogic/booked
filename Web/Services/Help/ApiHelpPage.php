<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class ApiHelpPage
{
    public static function Render(SlimWebServiceRegistry $registry, Slim\App $app): string
    {
        $builder = new StringBuilder();
        $head = <<<EOT
	<!DOCTYPE html>
	    <html>
	        <head>
	            <meta charset="utf-8"/>
	            <meta name="robots" content="noindex">
	            <title>Booked Scheduler API Documentation</title>
	              <link rel="shortcut icon" href="../favicon.png"/>
                  <link rel="icon" href="../favicon.png"/> 
	            <style type="text/css">
					body
					{
						margin: 10px;
						font: 14px Helvetica, "Helvetica Neue", "Lucida Grande", Verdana, Arial, sans-serif;
					}

					h1 {
						color: #fff;
						background-color: #36648B;
						line-height: 90px;
						padding-left: 20px;
					}

					h2 {
						background-color: #BCE27F;
						line-height: 30px;
						padding-left: 20px;
						border: solid 1px #8CC739;
					}
					.service {
						border: solid 1px #ccc;
						background-color: #ededed;
						padding: 6px;
						padding-top:0px;
						margin-bottom: 4px;
					}
					.code {
						font-family: courier;
					}
					#security {
						background-color: #FFFF99;
						border: solid 1px #CC9900;
						padding: 6px;
					}
					#security span {
						font-weight:bold;
					}

					a, a:visited {
						color:blue;
					}

                    .secure, .admin {
                        color:#ff0000;
                    }
	            </style>
	        </head>
	        <body>
	            <h1>Booked Scheduler API Documentation</h1>
EOT;

        $security = sprintf("<div id='security'>Pass the following headers for all secure service calls: <span>%s</span> and <span>%s</span></div>",
            WebServiceHeaders::SESSION_TOKEN, WebServiceHeaders::USER_ID);
        $builder->Append($head);

        $builder->Append($security);

        $builder->Append('<ul>');

        foreach ($registry->Categories() as $category) {
            $builder->Append("<li><a href='#{$category->Name()}'>{$category->Name()}</a></li>");
        }

        $builder->Append('</ul>');
        foreach ($registry->Categories() as $category) {
            $builder->Append("<a id='{$category->Name()}'></a><h2>{$category->Name()}</h2>");
            $builder->Append("<a href=''>Return To Top</a>");
            $builder->Append('<h3>POST Services</h3>');

            foreach ($category->Posts() as $service) {
                $builder->Append('<div class="service">');

                $md = $service->Metadata();
                $request = $md->Request();
                $builder->Append(self::EchoCommon($md, $service, $app));

                $builder->Append('<h4>Request</h4>');
                if (is_object($request)) {
                    $builder->Append('<pre class="code">' . json_encode($request, JSON_PRETTY_PRINT) . '</pre>');
                } elseif (is_null($request)) {
                    $builder->Append('No request');
                } else {
                    $builder->Append('Unstructured request of type <i>' . $request . '</i>');
                }

                $builder->Append('</div>');
            }

            $builder->Append('<h3>GET Services</h3>');

            foreach ($category->Gets() as $service) {
                $builder->Append('<div class="service">');
                $md = $service->Metadata();
                $builder->Append(self::EchoCommon($md, $service, $app));
                $builder->Append('</div>');
            }

            $builder->Append('<h3>DELETE Services</h3>');

            foreach ($category->Deletes() as $service) {
                $builder->Append('<div class="service">');
                $md = $service->Metadata();
                $builder->Append(self::EchoCommon($md, $service, $app));
                $builder->Append('</div>');
            }
        }

        $builder->Append('</body></html>');
        return $builder->ToString();
    }

    /**
     * @param SlimServiceMetadata $md
     * @param SlimServiceRegistration $endpoint
     * @param Slim\App $app
     */
    private static function EchoCommon(SlimServiceMetadata $md, $endpoint, Slim\App $app): string
    {
        $builder = new StringBuilder();
        $response = $md->Response();
        $parser = $app->getRouteCollector()->getRouteParser();
        $builder->Append("<h4>Name</h4>" . $md->Name());
        $builder->Append("<h4>Description</h4>" . str_replace("\n", "<br/>", $md->Description()));
        $builder->Append('<h4>Route</h4><pre>' . $parser->urlFor($endpoint->RouteName(), $endpoint->Params()) . '</pre>');

        if ($endpoint->IsSecure()) {
            $builder->Append('<h4 class="secure">This service is secure and requires authentication</h4>');
        }
        if ($endpoint->IsLimitedToAdmin()) {
            $builder->Append('<h4 class="admin">This service is only available to application administrators</h4>');
        }

        $builder->Append('<h4>Response</h4>');
        if (is_object($response)) {
            $builder->Append('<pre class="code">' . json_encode($response, JSON_PRETTY_PRINT) . '</pre>');
        } elseif (is_null($response)) {
            $builder->Append('No response');
        } else {
            $builder->Append('Unstructured response of type <i>' . $response . '</i>');
        }

        return $builder->ToString();
    }
}

