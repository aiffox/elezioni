<?php
class DB_Credentials {
    
    private static $host="localhost";
    private static $username="cazzatoandrea";
    private static $password="";
    private static $DBname="my_cazzatoandrea";
    /*
    private static $host="localhost";
    private static $username="root";
    private static $password="";
    private static $DBname="elezioni";
    */



    public static function getHost() {
        return self::$host;
    }
    public static function getUsername() {
        return self::$username;
    }
    public static function getPassword() {
        return self::$password;
    }
    public static function getDBname() {
        return self::$DBname;
    }
   //  public static function getTabella($nome){
   //      return self::$tabella[$nome];
   // }
   // public static function getListaTabelle(){
   //     return self::$tabella;
   // }
}
?>