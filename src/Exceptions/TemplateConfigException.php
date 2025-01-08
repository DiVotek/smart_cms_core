<?php

namespace SmartCms\Core\Exceptions;

class TemplateConfigException extends \Exception
{
   public static function notFound(string $name): self
   {
      return new self("Template config not found in template: $name");
   }

   public static function empty(string $name): self
   {
      return new self("Template config is empty in template: $name");
   }

   public static function nameNotExists(string $name): self
   {
      return new self("Template config name not exists in template: $name");
   }

   public static function descriptionNotExists(string $name): self
   {
      return new self("Template config description not exists in template: $name");
   }

   public static function authorNotExists(string $name): self
   {
      return new self("Template config author not exists in template: $name");
   }

   public static function versionNotExists(string $name): self
   {
      return new self("Template config version not exists in template: $name");
   }

   public static function themeNotExists(string $name): self
   {
      return new self("Template config theme not exists in template: $name");
   }

   public static function layoutsNotExists(string $name): self
   {
      return new self("Template config layouts not exists in template: $name");
   }

   public static function mainLayoutNotExists(string $name): self
   {
      return new self("Template config main layout not exists in template: $name");
   }

   public static function sectionsNotExists(string $name): self
   {
      return new self("Template config sections not exists in template: $name");
   }

   public static function menuSectionNameNotExists(string $name): self
   {
      return new self("Template config menu section name not exists in template: $name");
   }

   public static function menuSectionIconNotExists(string $name, string $template): self
   {
      return new self("Template config menu section icon not exists in section: $name in template: $template");
   }

   public static function menuSectionDescriptionNotExists(string $name, string $template): self
   {
      return new self("Template config menu section description not exists in section: $name in template: $template");
   }

   public static function menuSectionSchemaNotExists(string $name, string $template): self
   {
      return new self("Template config menu section schema not exists in section: $name in template: $template");
   }

   public static function translatesNotValid(string $name): self
   {
      return new self("Template config translates not valid in template: $name");
   }
}
