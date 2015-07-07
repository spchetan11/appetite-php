<?php

define('MYSQL_SERVERNAME', "localhost");
define('MYSQL_UNAME', "usr_mobiroster");
define('MYSQL_PWD', "Centurion12");
define('MYSQL_DB', "mobirost_hangouts");


// setting
define('SETTING_ERROR', true);
define('V_MIN_ANDROID',1);
define('V_MAX_ANDROID',1);
define('V_MIN_IOS',1);
define('V_MAX_IOS',1);
define('V_MIN_WEB',1);
define('V_MAX_WEB',1);

define('URL_DATA', "./data");
define('URL_GALLERY', "./data/gallery");
define('URL_MENU', "./data/menu/thumbs");
define('URL_LOGIN', "./data/login");
define('URL_EVENTS', "./data/events");
define('URL_OFFERS', "./data/offers" );

define('KEY_BASE_URL_GALLERY', "/data/gallery");
define('KEY_BASE_URL_MENU', "/data/menu/thumbs");
define('KEY_BASE_URL_LOGIN', "/data/login");
define('KEY_BASE_URL_EVENTS', "/data/events");
define('KEY_BASE_URL_OFFERS', "/data/offers");

//admin config
define('DATA', "data");
define('ADMIN', "admin");
define('MENU_SECTIONS', "menu_sections");
define('MENU_DATA', "menu_data");
define('ABOUT', "about");
define('GALLERY', "gallery");
define('OFFERS', "offers");
define('SPECIALS_SECTIONS', "specials_sections");
define('SPECIALS_DATA', "specials_data");
define('EVENTS', "events");


define('CONS_GUEST_USER', 'guest');

define('V_SERVER_STATUS_CODE', 1);

//
define('V_ONLINE', 1);
define('V_OFFLINE', 0);

//Device constants
define('D_ANDROID', 0);
define('D_IOS', 1);
define('D_WEB', 2);

//User types
define('USR_CUSTOMER', 0);
define('USR_ADMIN', 1);

//response keys
define('KEY_STATUS', "status");
define('KEY_ERROR_CODE', "error_code");
define('KEY_ERROR_MESSAGE', "error_message");
define('KEY_MESSAGE', "message");
define('KEY_SERVER_STATUS', "server_status");
define('KEY_MODE', "mode");
define('KEY_MIN_VERSION', "min_version");
define('KEY_MAX_VERSION', "max_version");
define('KEY_CATEGORIES', "categories");
define('KEY_UPDATE_KEYS', "update_keys");
define('KEY_UPDATE_KEY', "update_key");
define('KEY_MENU', "menu");
define('KEY_ABOUT', "about");
define('KEY_TIMINGS', "timings");
define('KEY_FROM', "from");
define('KEY_TO', "to");
define('KEY_LOCATION', "location");
define('KEY_EVENTS', "events");
define('KEY_SPECIAL', "special");
define('KEY_GALLERY', "gallery");
define('KEY_OFFERS', "offers");
define('KEY_ITEMS', "items");
define('KEY_URL', "url");
define('KEY_DATA', "data");
define('KEY_BASE_URL', "base_url");
define('KEY_EMAIL', "email");
define('KEY_PASSWORD', "password");
define('KEY_EMAIL_CHECK', "email_check");
define('KEY_FIRST_NAME', "firstname");
define('KEY_MIDDLE_NAME', "middlename");
define('KEY_LAST_NAME', "lastname");
define('KEY_PWD_HASH', "pwdHash");
define('KEY_EXISTING_OFFER', "existing_offer");
define('KEY_OFFER_SET', "offer_set");
define('KEY_USER_DETAILS', "user_details");
define('KEY_REDEEM_OFFER', "redeem_offer");
define('KEY_SET_OFFER_DETAILS', "set_offer_details");
define('KEY_EMAIL_STATUS', "email_status");




//Tables
define('TAB_CATEGORIES', "tab_categories");
define('TAB_SESSION_KEYS', "tab_session_keys");
define('TAB_UPDATE_KEYS', "tab_update_keys");
define('TAB_USERS', "tab_users");
define('TAB_MENU_SECTIONS', "tab_menu_sections");
define('TAB_MENU_DATA', "tab_menu_data");
define('TAB_ABOUT', "tab_about");
define('TAB_EVENTS', "tab_events");
define('TAB_SPECIAL_SECTIONS',"tab_special_sections");
define('TAB_SPECIAL_DATA',"tab_special_data");
define('TAB_EVENT_IMAGES_EXTRAS', "tab_event_images_extras");
define('TAB_PASSWORD_RESET', "tab_password_reset");
define('TAB_OFFERS',"tab_offers");
define('TAB_GALLERY',"tab_gallery");
define('TAB_SET_OFFER', "tab_set_offer");

//Table columns
define('COL_ID', "id");
define('COL_USER_ID', "user_id");
define('COL_USER_NAME', "user_name");
define('COL_NAME', "name");
define('COL_FULL_NAME', "full_name");
define('COL_USER_TYPE', "user_type");
define('COL_DEVICE_ID', "deviceid");
define('COL_KEY', "key");
define('COL_TIMESTAMP', "timestamp");
define('COL_CATEGORY_ID', "category_id");
define('COL_UPDATE_ID', "update_id");
define('COL_ADDED_ON', "added_on");
define('COL_EXPIRES_ON', "expires_on");
define('COL_STARTS_AT', "starts_at");
define('COL_EMAIL', "email");
define('COL_PASSWORD', "password");
define('COL_DOB', "dob");
define('COL_SECTION_ID', "section_id");
define('COL_TITLE', "title");
define('COL_DESCRIPTION', "description");
define('COL_PRICE', "price");
define('COL_THUMBNAIL', "thumb");
define('COL_COMPANY_NAME', "company_name");
define('COL_PHONE', "phone");
define('COL_FB_LINK', "facebook_link");
define('COL_LATITUDE', "latitude");
define('COL_LONGITUDE', "longitude");
define('COL_PLACE_TAGS', "place_tags");
define('COL_ADDRESS', "address");
define('COL_TAG', "tag");
define('COL_IMAGE', "image");
define('COL_FILENAME', "filename");
define('COL_THUMBNAIL_IMAGE', "thumb_image");
define('COL_OFFER_ID', "offerid");
define('COL_ENDED_ON', "ended_on");
define('COL_AVAILABLE_TIME', "available_time");
define('COL_TIMING_SUN_FROM', "tm_sun_from");
define('COL_TIMING_MON_FROM', "tm_mon_from");
define('COL_TIMING_TUE_FROM', "tm_tue_from");
define('COL_TIMING_WED_FROM', "tm_wed_from");
define('COL_TIMING_THU_FROM', "tm_thu_from");
define('COL_TIMING_FRI_FROM', "tm_fri_from");
define('COL_TIMING_SAT_FROM', "tm_sat_from");
define('COL_TIMING_SUN_TO', "tm_sun_to");
define('COL_TIMING_MON_TO', "tm_mon_to");
define('COL_TIMING_TUE_TO', "tm_tue_to");
define('COL_TIMING_WED_TO', "tm_wed_to");
define('COL_TIMING_THU_TO', "tm_thu_to");
define('COL_TIMING_FRI_TO', "tm_fri_to");
define('COL_TIMING_SAT_TO', "tm_sat_to");
define('COL_WILL_EXPIRE_ON', "will_expire_on");
define('COL_EVENT_ID', "event_id");
define('COL_RESET_CODE', "resetCode");
define('COL_IS_DELETED', "is_deleted");
define('COL_FB_ID', "facebook_id"); //COL_FB_EMAIL
define('COL_FB_EMAIL', "fb_email");


//param keys
define('P_PLATFORM_CODE', "platformCode");
define('P_EMAIL', "email");
define('P_FB_EMAIL', "facebook_email");
define('P_USER_NAME', "user_name");
define('P_PASSWORD', "password");
define('P_NAME', "name");
define('P_DOB', "dob");
define('P_SESSION_KEY', "sk");
define('P_HAS_EXISTING_SESSION', "hasExistingSession");
define('P_ACTIVE_OFFER_ID', "activeOfferId");
define('P_EVENT_IMAGES_EXTRAS', "event_images_extras");
define('P_ADMIN', "admin");
define('P_FB_ID', "facebook_id");
define('P_FULL_NAME', "full_name");
define('P_PARAM', "param");


//services
define('M_INIT', "init");
define('M_LOGIN', "login");
define('M_GET_MENU', "getMenu");
define('M_GET_ABOUT_DETAILS', "getAboutDetails");
define('M_GET_EVENTS',"getEvents");
define('M_GET_SPECIAL', "getSpecial");
define('M_GET_OFFERS', "getOffers");
define('M_GET_GALLERY', "getGallery");
define('M_SET_OFFER', "setOffer");
define('M_REGISTER', "register");
define('M_CONNECT_FACEBOOK', "connectFacebook");
define('M_FORGOT_PASSWORD', "forgotPassword");
define('M_REDEEM_OFFER', "redeemOffer");
define('M_GET_ALL_OFFERS', "getAllOffers");
define('M_GET_ALL_EVENTS', "getAllEvents");
define('M_VERIFY_EMAIL', "verifyEmail");
define('M_UPDATE_FACEBOOK_DETAILS', "updateFacebookDetails");

// messages
define('MSG_EMAIL_NOT_REGISTERED', "Email has not been registered before");

//response values
define('R_STATUS_SUCCESS', "success");
define('R_STATUS_FAILED', "failed");
define('R_STATUS_ONLINE', "online");
define('R_STATUS_OFFLINE', "offline");

define('E_UNKNOWN', -1);
define('E_INSUFFICIENT_PARAMS', 2);
define('E_UNKNOWN_SERVICE_REQUEST', 3);
define('E_INVALID_CREDENTIALS', 4);
define('E_NO_SESSION_KEY', 5);
define('E_INVALID_SESSION_KEY', 6);
define('E_ERROR_FETCHING_MENU', 7);
define('E_ERROR_FETCHING_ABOUT', 8);
define('E_ERROR_FETCHING_EVENTS',9);
define('E_ERROR_FETCHING_SPECIAL',10);
define('E_ERROR_FETCHING_GALLERY',11);
define('E_ERROR_FETCHING_OFFERS',12);
define('E_ERROR_SETTING_OFFERS',13);
define('E_USER_ALREADY_EXISTS', 14);
define('E_ERROR_REGISTRING_USER', 15);
define('E_OFFER_ALREADY_SET', 16);
define('E_USER_DOES_NOT_EXIST', 17);
define('E_INVALID_EMAIL_FORMAT', 18);
define('E_ACTIVE_OFFER_ID_NOT_PROVIDED', 19);
define('E_ERROR_REDEEMING_OFFER', 20);
define('E_ACCESS_DENIED', 21);
define('E_INVALID_USER', 22);
define('E_NO_OFFERS_AVAILABLE', 23);
define('E_ERROR_UPDATING_DETAILS', 24);
define('E_NO_EVENTS_AVAILABLE', 25);

define('EM_UNKNOWN', "Unknown");
define('EM_INSUFFICIENT_PARAMS', "Insufficient Parameters.");
define('EM_UNKNOWN_SERVICE_REQUEST', "Unknown request.");
define('EM_INVALID_CREDENTIALS', "Wrong email/password.");
define('EM_NO_SESSION_KEY', "No session key provided.");
define('EM_INVALID_SESSION_KEY', "Invalid session key. Login again.");
define('EM_ERROR_FETCHING_MENU', "Error fetching menu.");
define('EM_ERROR_FETCHING_ABOUT', "Error fetching company data.");
define('EM_ERROR_FETCHING_EVENTS',"Error fetching events");
define('EM_ERROR_FETCHING_SPECIAL',"Error fetching special");
define('EM_ERROR_FETCHING_GALLERY',"Error fetching gallery");
define('EM_ERROR_FETCHING_OFFERS',"Error fetching offers");
define('EM_ERROR_SETTING_OFFERS',"Error setting offers");
define('EM_USER_ALREADY_EXISTS', "User already exists");
define('EM_ERROR_REGISTRING_USER', "Error registering user");
define('EM_OFFER_ALREADY_SET', "Offer already set to user");
define('EM_USER_DOES_NOT_EXIST', "User does not exist. Sign Up required");
define('EM_INVALID_EMAIL_FORMAT', "Invalid email format");
define('EM_ACTIVE_OFFER_ID_NOT_PROVIDED', "Active offer id not provided");
define('EM_ERROR_REDEEMING_OFFER', "error redeeming offer");
define('EM_ACCESS_DENIED', "Access denied");
define('EM_INVALID_USER', "Invalid User");
define('EM_NO_OFFERS_AVAILABLE', "Sorry! There are no offers available right now. Please check again later.");
define('EM_ERROR_UPDATING_DETAILS', "Error updating details");
define('EM_NO_EVENTS_AVAILABLE', "Sorry! There are no events available right now. Please check again later.");

define('TAG_VEG', 1);
define('TAG_NON_VEG', 2);
define('TAG_DRINKS', 3);
define('TAG_WIFI', 4);
define('TAG_OUTDOOR', 5);
define('TAG_INDOOR', 6);
define('TAG_SMOKING', 7);
define('TAG_NO_SMOKING', 8);

define('CATEGORY_MENU', "menu");
define('CATEGORY_GALLERY', "gallery");
define('CATEGORY_OFFERS', "offers");
define('CATEGORY_ABOUT', "about");
define('CATEGORY_SPECIALS', "specials");
define('CATEGORY_EVENTS', "events");



define('EMAIL_INVALID_FORMAT',0);
define('EMAIL_OK',1);
define('EMAIL_EXISTS',2);

define('TEMP_SK', "uADvon3498jAnjvsA65k5vs");
?>