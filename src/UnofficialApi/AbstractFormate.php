<?php

namespace UnofficialApi;

abstract class AbstractFormate
{
    abstract protected function setProxy(string $ip, int $port, string $login = null, string $password = null);
    abstract protected function loadSession(string $id);
    abstract protected function getDeviceId();
    abstract protected function callApi(string $method, array $headers, array $param, $desctop);
  
    /**
     * Data params body send
     */
    public function userAuth()
    {
        return [
            '$gpb' => 'badoo.bma.BadooMessage',
            'version' => 1,
            'message_type' => 15,
            'message_id' => 5,
            'body' => [
                [
                    '$gpb' => 'badoo.bma.MessageBody',
                    'message_type' => 15,
                    'server_login_by_password' => [
                        '$gpb' => 'badoo.bma.ServerLoginByPassword',
                        'user' => $this->login,
                        'password' => $this->password
                    ]
                ]
            ],
            'is_background' => false
        ];
    }

    /**
     * Data params body send
     *
     */
    public function createSession()
    {
        return [
            'header' => [
                'content-type' => 'json',
                'user-agent' => $this->UserAgent,
                'x-user-agent' => $this->UserAgent,
                'x-use-session-cookie' => 1,
                'x-desktop-web' => 1,
                'x-message-type' => 2,
                'cookie' => 'device_id=' . $this->device_id
            ],
            'param' => [
                '$gpb' => 'badoo.bma.BadooMessage',
                'version' => 1,
                'message_type' => 2,
                'message_id' => 1,
                'body' => [
                    [
                        '$gpb' => 'badoo.bma.MessageBody',
                        'message_type' => 2,
                        'server_app_startup' => [
                            '$gpb' => 'badoo.bma.ServerAppStartup',
                            'open_udid' => $this->device_id,
                            'screen_height' => 667,
                            'screen_width' => 375,
                            'app_version' => '6.465.1',
                            'locale' => 'ru',
                            'language' => 0,
                            'user_agent' => $this->UserAgent,
                            'can_send_sms' => false,
                            'external_provider_redirect_url' => 'https=>//badoo.com/cb.html',
                            'app_platform_type' => 4,
                            'app_product_type' => 100,
                            'app_build' => 'Webapp',
                            'app_name' => 'BMA/Webapp',
                            'app_domain' => 'com.badoo',
                            'external_provider_apps' => [28, 26],
                            'build_configuration' => 2,
                            'hotpanel_session_id' => '',
                            'device_info' => ['$gpb' => 'badoo.bma.DeviceInfo', 'screen_density' => 2],
                            'verification_provider_support' => [1, 4, 3, 2, 15, 12, 14],
                            'start_source' => ['$gpb' => 'badoo.bma.ServerAppStatsStartSource', 'type' => 0, 'http_referrer' => '', 'start_screen' => 33, 'current_url' => 'https=>//badoo.com/landing']
                        ]
                    ]
                ],
                'is_background' => false
            ]
        ];
    }


    /**
     * Data params body send
     *
     */
    public function searchUser(int $gender, int $start, int $end, int $count, int $offset, string $country): array
    {
        $this->defaultHeader['x-message-type'] = 420;
        return [
            'version' => 1,
            'message_type' => 420,
            'message_id' => 13,
            'body' => [
                [
                    'message_type' => 503,
                    'server_save_search_settings' => [
                        'context_type' => 2,
                        'settings' => [
                            'age' => [
                                'start' => $start, 'end' => $end
                            ],
                            'distance' => [
                                'fixed_end' => !empty($country) ? $country : '50_169_2351_c'
                            ],
                            'tiw_phrase_id' => 10002,
                            'gender' => [$gender]
                        ]
                    ]
                ], [
                    'message_type' => 245,
                    'server_get_user_list' => [
                        'folder_id' => 25,
                        'user_field_filter' => [
                            'projection' => [250, 200, 210, 230, 310, 330, 530, 540, 340, 331, 290, 291, 550, 580, 670, 660]
                        ],
                        'offset' => $offset,
                        'promo_block_request_params' => [
                            ['count' => 1, 'position' => 2],
                            ['count' => 1, 'position' => 1]
                        ],
                        'preferred_count' => $count
                    ]
                ]
            ],
            'is_background' => false
        ];
    }

    /**
     * Data params body send
     *
     */
    public function getCity(string $city)
    {
        $this->defaultHeader['x-message-type'] = 29;
        return [
            'message_type' => 29,
            'message_id' => 43,
            'version' => 1,
            'is_background' => false,
            'body' => [
                [
                    'message_type' => 29,
                    'server_search_locations' => [
                        'with_countries' => true,
                        'query' => $city
                    ]
                ]
            ]
        ];
    }

    /**
     * Data params body send
     *
     */
    public function getVisitors()
    {
        $this->defaultHeader['x-message-type'] = 245;
        return [
            'version' => 1,
            'message_type' => 245,
            'message_id' => 122, 'body' => [
                [
                    'message_type' => 245, 'server_get_user_list' => [
                        'source' => 22,
                        'folder_id' => 8,
                        'user_field_filter' => [
                            'projection' => [200, 340, 330, 331, 250, 270, 290, 291, 570, 550, 280, 210, 230, 91, 92, 93, 760, 761, 580, 660, 670]
                        ],
                        'preferred_count' => 150,
                        'promo_block_request_params' => [
                            ['count' => 1, 'position' => 2], ['count' => 1, 'position' => 1]
                        ],
                        'is_for_tabbed_ui' => true,
                        'offset' => 0
                    ]
                ]
            ],
            'is_background' => false
        ];
    }

    /**
     * Data params body send
     *
     */
    public function likeUser(string $userId)
    {
        $this->defaultHeader['x-message-type'] = 80;
        return [
            'version' => 1,
            'message_type' => 80,
            'message_id' => 29,
            'body' => [
                [
                    'message_type' => 80,
                    'server_encounters_vote' => [
                        'person_id' => $userId,
                        'vote' => 2,
                        'vote_source' => 2
                    ]
                ]
            ], 'is_background' => false
        ];
    }

    /**
     * Data params body send
     *
     */
    public function sendMessage(string $userId, string $text)
    {
        $this->defaultHeader['x-message-type'] = 104;
        return [
            '$gpb' => 'badoo.bma.BadooMessage',
            'version' => 1,
            'message_type' => 104,
            'message_id' => 67,
            'body' => [
                [
                    '$gpb' => 'badoo.bma.MessageBody',
                    'message_type' => 104,
                    'chat_message' => [
                        '$gpb' => 'badoo.bma.ChatMessage',
                        'from_person_id' => $this->user,
                        'message_type' => 1,
                        'mssg' => $text,
                        'read' => false,
                        'to_person_id' => $userId,
                        'uid' => 'TEMP_ID:1',
                        'context' => 26
                    ]
                ]
            ],
            'is_background' => false
        ];
    }

    /**
     * Data params body send
     *
     */
    public function getUser(string $user): array
    {
        $this->defaultHeader['x-message-type'] = 403;
        return [
            '$gpb' => 'badoo.bma.BadooMessage',
            'version' => 1,
            'message_type' => 403,
            'message_id' => 40,
            'body' => [
                [
                    '$gpb' => 'badoo.bma.MessageBody',
                    'message_type' => 403,
                    'server_get_user' => [
                        '$gpb' => 'badoo.bma.ServerGetUser',
                        'user_id' => $user ?: $this->user,
                        'visiting_source' => [
                            '$gpb' => 'badoo.bma.ProfileVisitingSource',
                            'person_id' => $user ?: $this->user,
                            'section_id' => '0',
                            'visiting_source' => 26
                        ],
                        'client_source' => 26,
                        'user_field_filter' => [
                            '$gpb' => 'badoo.bma.UserFieldFilter',
                            'projection' => [210, 370, 660, 732, 431, 93, 530, 220, 230, 301, 420, 250, 610, 580, 290, 304, 550, 200, 330, 331, 310, 50, 490, 470, 870, 750, 460, 560, 291, 100, 360, 492, 1110, 870, 1252, 1254, 1260, 650, 770, 670, 600, 320, 340, 480, 311, 1130, 1200, 730, 663],
                            'request_albums' => [
                                [
                                    '$gpb' => 'badoo.bma.ServerGetAlbum',
                                    'album_type' => 2
                                ], [
                                    '$gpb' => 'badoo.bma.ServerGetAlbum',
                                    'album_type' => 4
                                ], [
                                    '$gpb' => 'badoo.bma.ServerGetAlbum',
                                    'album_type' => 7
                                ]
                            ],
                            'request_interests' => [
                                '$gpb' => 'badoo.bma.ServerInterestsGet',
                                'user_id' => $user ?: $this->user,
                                'limit' => 500
                            ],
                            'quick_chat_request' => [
                                '$gpb' => 'badoo.bma.QuickChatRequest',
                                'message_count' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'is_background' => false
        ];
    }
}
