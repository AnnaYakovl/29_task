<?php
class View
{
	function generate($template_view, $token = null, $authorised = null, $vk = null)
	{
		include 'pages/'.$template_view;

		if ($template_view == 'registrationPage.php' ||
				$template_view =='loginPage.php')
		{
			$url = 'http://oauth.vk.com/authorize';
					
			$params = array(
				'client_id'     => id,
				'redirect_uri'  => uri,
				'response_type' => 'code'
			);
			
			echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Аутентификация через ВКонтакте</a></p>';
		}		
		
		
	}
}