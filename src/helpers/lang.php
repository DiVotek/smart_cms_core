<?php

if (! function_exists('_actions')) {
   function _actions(string $key): string
   {
      return __('smart_cms::actions.' . $key);
   }
}

if (! function_exists('strans')) {
   function strans(string $key): string
   {
      return __('smart_cms::' . $key);
   }
}

if (! function_exists('_columns')) {
   function _columns(string $key): string
   {
      return __('smart_cms::columns.' . $key);
   }
}

if (! function_exists('_fields')) {
   function _fields(string $key): string
   {
      return __('smart_cms::fields.' . $key);
   }
}

if (! function_exists('_hints')) {
   function _hints(string $key): string
   {
      return __('smart_cms::hints.' . $key);
   }
}

if (! function_exists('_nav')) {
   function _nav(string $key): string
   {
      return __('smart_cms::navigation.' . $key);
   }
}
