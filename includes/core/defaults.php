<?php

    // Default plugin information
    
    return array(
        "user_inactivity_time"          => 15, 
        "user_activity_time"            => 24,
        "last_user"                     => "on",
        "bbpress_statistics"            => "on",
        "title_text_currently_active"   => "There is currently %COUNT_ACTIVE_USERS% %USER_USERS% and %COUNT_ACTIVE_GUSERS% %GUEST_GUESTS% online",
        "title_text_last_x_hours"       => "Activity within the past %HOURS% hours: %COUNT_ALL_USERS% %ALL_USER_USERS% and %COUNT_ALL_GUSERS% %ALL_GUEST_GUESTS%",
        "forum_display_option"          => array("after_forums_index"),
        "bbpress_statistics_merge"      => "on",
        "extra_user_online_status"      => "off",
        "extra_enable_shortcode"        => "off",
        "extra_enable_whitelist"        => "off",
        "extra_whitelist_fields_array"  => "b,i,u,s,center,right,left,justify,quote,url,img,youtube,vimeo,note,li,ul,ol,list",
        "user_display_format"           => "display_as_username",
        "disable_css"                   => "off",
        "extra_keep_db"                 => "off",
        "most_users_online"             => "on",
        "user_group_key"                => "on",
        "record_users"                  => array("users" => 1, "date" => date('Y-m-d H:i:s') ),
        "stats_to_display"              => array("last_x_hours","last_x_mins"),
        "user_display_limit"            => 150,
        "user_display_limit_link"       => -1,
        "track_guests"                  => "on",
        "title_text_latestuser"         => "Welcome to our newest member, %LATEST_USER%",
        "title_text_mostusers"          => "Most users ever online was %USER_RECORD% on %USER_RECORD_DATE% %USER_RECORD_TIME%",
        "title_text_bbpress_stats"      => "Additional Forum Statistics",
        "title_text_bbpress_stats_form" => "Threads: , Posts: , Members: "
    );