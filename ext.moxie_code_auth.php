<?php

if (!defined('EXT'))
{
  exit('Invalid file request');
}

class Moxie_code_auth
{
  var $settings        = array();
  var $name            = 'Moxiecode Authentication';
  var $version         = '0.1.0';
  var $description     = 'Restricts Access to TinyMCE File Manager and Image Manager';
  var $settings_exist  = 'n';
  var $docs_url        = '';
  
  var $_sess_name      = 'isLoggedIn';
  
  function Moxie_code_auth($settings = '')
  {
  }
  
  function create_moxiecode_session()
  {
    $this->_start_session();
    $_SESSION[$this->_sess_name] = true;
  }
  
  function destroy_moxiecode_session()
  {
    $this->_start_session();
    if (isset($_SESSION[$this->_sess_name]))
    {
      unset($_SESSION[$this->_sess_name]);
      if (empty($_SESSION))
      {
        session_destroy(); 
      }
    }
  }
  
  function activate_extension()
  {
    global $DB;
    
    $DB->query($DB->insert_string('exp_extensions',
        array(
        'extension_id' => '',
        'class'        => __CLASS__,
        'method'       => 'create_moxiecode_session',
        'hook'         => 'cp_member_login',
        'settings'     => '',
        'priority'     => 10,
        'version'      => $this->version,
        'enabled'      => 'y'
        )
      )
    );
    
    $DB->query($DB->insert_string('exp_extensions',
        array(
        'extension_id' => '',
        'class'        => __CLASS__,
        'method'       => 'destroy_moxiecode_session',
        'hook'         => 'cp_member_logout',
        'settings'     => '',
        'priority'     => 10,
        'version'      => $this->version,
        'enabled'      => 'y'
        )
      )
    );
  }
  
  function update_extension($current='')
  {
    global $DB;
    
    if ($current == '' OR $current == $this->version)
    {
      return FALSE;
    }
    
    $DB->query("UPDATE exp_extensions 
                SET version = '".$DB->escape_str($this->version)."' 
                WHERE class = '" . __CLASS__ . "'");
  }
  
  function disable_extension()
  {
    global $DB;
    
    $DB->query("DELETE FROM exp_extensions WHERE class = '" . __CLASS__ . "'");
  }
  
  function _start_session()
  {
    if (!isset($_SESSION))
    {
      session_start();
    }
  }
}