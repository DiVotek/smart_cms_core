# SmartCms Core Events Documentation

This document explains the events available in the **smart-cms/core** package. These events allow developers to modify, extend, or hook into the functionality provided by the package.

---

## What Are Package Events?

Package events are custom events dispatched during key moments of execution within the package. They enable developers to extend or override default behavior by listening to these events and injecting their own logic.

---

## Available Events

Below is a list of events provided by the package, their purposes, and examples of how to use them.

---

### 1. `cms.page.content.building`

**Description:**
This event is dispatched when the content of a page is being built. Developers can listen to this event to modify or extend the page content before it is rendered.

### 2. `cms.admin.navigation.resources`

**Description:**
This event is dispatched when the resources for the admin navigation are being built. Developers can listen to this event to add or remove resources from the admin navigation.

### 3. `cms.admin.navigation.groups`

**Description:**
This event is dispatched when the groups for the admin navigation are being built. Developers can listen to this event to add or remove groups from the admin navigation.

### 4. `cms.admin.navigation.pages`

**Description:**
This event is dispatched when the pages for the admin navigation are being built. Developers can listen to this event to add or remove pages from the admin navigation.

### 5. `cms.admin.navigation.settings_pages`

**Description:**
This event is dispatched when the settings pages for the admin navigation are being built. Developers can listen to this event to add or remove settings pages from the admin navigation.

### 6. `cms.admin.widgets`

**Description:**
This event is dispatched when the widgets for the admin dashboard are being built. Developers can listen to this event to add or remove widgets from the admin dashboard.

### 7. `cms.admin.update`

**Description:**
This event is dispatched when the package is being updated. Developers can listen to this event to perform additional actions during the update process.

### 8. `cms.page.construct`

**Description:**
This event is dispatched when a page is being constructed. Developers can listen to this event to modify or extend the page data before it is rendered.

### 9. `cms.page.constructed`

**Description:**
This event is dispatched after a page has been constructed. Developers can listen to this event to perform additional actions after the page has been rendered.

### 10. `cms.page.transform`

**Description:**
This event is dispatched when a page DTO is being transformed. Developers can listen to this event to modify or extend the page DTO before it is returned.

### 11. `cms.page-entity.transform`

**Description:**
This event is dispatched when a page entity DTO is being transformed. Developers can listen to this event to modify or extend the page entity DTO before it is returned.

### 12. `cms.sitemap.generate`

**Description:**
This event is dispatched when the sitemap is being generated. Developers can listen to this event to modify or extend the sitemap data before it is returned.

### 13. `cms.page.get`

**Description:**
This event is dispatched when a page is being retrieved. Developers can listen to this event to modify or extend the page data before it is returned.

### 14. `cms.robots.generate`

**Description:**
This event is dispatched when the robots.txt file is being generated. Developers can listen to this event to modify or extend the robots.txt data before it is returned.

### 15. `cms.admin.menu.building`

**Description:**
This event is dispatched when the menu is being built. Developers can listen to this event to modify or extend the admin menu before it is rendered.

### 16. `cms.admin.schema.build`

**Description:**
This event is dispatched when the schema is being built. Developers can listen to this event to modify or extend the schema before it is rendered.

### 17. `cms.admin.schema.parse`

**Description:**
This event is dispatched when the schema is being parsed. Developers can listen to this event to modify or extend the schema before it is rendered.
