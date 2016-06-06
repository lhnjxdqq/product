<?php
/**
 * 数据验证封装
 *
 * @author  shuishou
 */

class   Validate {
     
    /**
     * 验证提交数据是否为空
     *
     * @param   string  $text    文本
     * @param   string  $message 文本
     * $throw   ApplicationException 校验数据为空时抛出异常
     */
     static public  function testNull ($text,$message, $toUrl = '') {
         
         if(empty($text)){
             
             throw   new ApplicationException(array(
             	'message'	=> $message,
             	'to_url'	=> $toUrl,
             ));
         }
     }    
     
     /**
      * 验证数据中是否存在某元素
      *
      * @param   string  $text    文本
      * @param   array   $data    数组
      * @param   string  $message 文本
      * $throw   ApplicationException 校验数据不存在时抛出异常      
      */
     static public  function isExist ($text,array $data,$message) {
         
         if(!in_array($text,$data)) {

             throw  new ApplicationException($message);
         }
     }  
     
     /**
      * 验证时间格式(Y-m-d H:i:s)
      *
      * @param   string  $time    文本
      * @param   string  $message 文本
      * $throw   ApplicationException 校验数据为时间格式错误时抛出异常      
      */
     static public  function validateTime ($time,$message) {
         
         if(!strtotime($time)){
             
             throw  new ApplicationException($message);
         }
     } 
     
     /**
      * 验证是否是数字
      *
      * @param   intval  $data    文本
      * @param   string  $message 文本
      * $throw   ApplicationException 校验数据不是数字抛出异常      
      */
     static public  function validateInt ($data,$message) {
         
         if(!is_numeric($data)){
             
             throw  new ApplicationException($message);
         }
     }
}