<?php

return [

	'standard'	=> [
		/*
		|--------------------------------------------------------------------------
		| Default transcode settings
		|--------------------------------------------------------------------------
		*/
		'defaults'              => [
			"input"         => null,
			"region"        => "australia-sydney",
			"pass_through"  => [
				"job_type" => null,
				"job_id"   => null
			],
			"notifications" => Config::get('zencoder::receivingUrl'),
			"outputs"       => [
				[
					"label"         => "web",
					"quality"       => 3,
					"audio_quality" => 3,
					"format"        => "mp4",
					"size"          => "1024x576",
					"frame_rate"    => 25,
					"aspect_mode"   => "pad",
					"url"           => null,
					"upscale"       => true,
					"thumbnails"    => [
						[
							"label"       => "thumbs",
							"times"       => [2],
							"base_url"    => s3ImagePath('videos/'),
							"size"        => "1024x576",
							"aspect_mode" => "pad",
							"prefix"      => "thumbnail",
							"filename"    => null
						],
					]
				]
			]
		]
	]

];