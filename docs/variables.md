# Supported Variable Types Documentation

This document provides an overview of the supported variable types that can be defined in the `config.yaml` file for sections or layouts. These variables can be used to dynamically fill data in application using admin panel.

---

## Basics

Each field supports the following properties:

-   **name:** The name of the field. This is used as the key to store the value in the database.
-   **type:** The type of the field. This determines the input method and validation rules for the field.
-   **label:** The label of the field. This is displayed to the user in the admin panel.
-   **default:** The default value of the field. This is used if no value is provided by the user.
-   **required:** A boolean value indicating whether the field is required or not.
    Required fields are marked with an asterisk (\*) in the admin panel. Required property will not work with field types: `heading`, `description`, `pages`

## Supported Variable Types

Below is a list of variable types supported in the `config.yaml` file, along with their descriptions, structure, and examples.

---

### **1. Text**

**Description:**
A simple text field that accepts string input. If type is not specified, it defaults to `text`.

**Structure:**

```yaml
type: text
```

### **2. Number**

**Description:**
A simple number field that accepts integer input.

**Structure:**

```yaml
type: number
```

### **3. Bool**

**Description:**
A simple bool field that accepts true or false.

**Structure:**

```yaml
type: bool
```

### **4. Image**

**Description:**
An image field that accepts uploaded image files. Each image will be converted to .webp format.

**Structure:**

```yaml
type: image
```

### **5. File**

**Description:**
A file field that accepts uploaded files.

**Structure:**

```yaml
type: file
```

### **6. Form**

**Description:**
A select field that accepts values from a Form section.

**Structure:**

```yaml
type: form
```

### **6. Heading**

**Description:**
A field that used to display headings on user choice in correct order. Pair with `heading` component. Admin can choice from `h1` to `h3` and can use seo heading or page name or type custom text.

**Structure:**

```yaml
type: heading
```

### **7. Description**

**Description:**
A field that used to display description on user choice in correct order. Pair with `description` component. Admin can use seo heading or summary or type custom text.

**Structure:**

```yaml
type: description
```

### **8. Socials**

**Description:**
A field that used to display social links. Social links can be added from admin panel.

**Structure:**

```yaml
type: socials
```

### **9. Phone**

**Description:**
A field that used to display phone number. Phone number can be added from admin panel.

**Structure:**

```yaml
type: phone
```

### **10. Phones**

**Description:**
Same as `phone` but can add multiple phone numbers.

**Structure:**

```yaml
type: phones
```

### **11. Email**

**Description:**
A field that used to display email address. Email address can be added from admin panel.

**Structure:**

```yaml
type: email
```

### **12. Emails**

**Description:**
Same as `email` but can add multiple email addresses.

**Structure:**

```yaml
type: emails
```

### **13. Address**

**Description:**
A field that used to display address. Address can be added from admin panel in settings section.

**Structure:**

```yaml
type: address
```

### **14. Addresses**

**Description:**
Same as `address` but support multiple values.

**Structure:**

```yaml
type: addresses
```

### **15. Schedule**

**Description:**
A field that used to display schedule. Schedule can be added from admin panel in settings section.

**Structure:**

```yaml
type: schedule
```

### **16. Schedules**

**Description:**
Same as `schedule` but support multiple values.

**Structure:**

```yaml
type: schedules
```

### **17. Menu**

**Description:**
A field that used to display array of menu items. Menu and its items can be added from admin panel in Menu section.

**Structure:**

```yaml
type: menu
```

### **18. Page**

**Description:**
A field that used to display page. Page can be added from admin panel in Pages section.

**Structure:**

```yaml
type: page
```

### **19. Pages**

**Description:**
Same as `page` but support multiple values with various options and filters.

**Structure:**

```yaml
type: pages
```

### **20. Array**

**Description:**
A field that used to combine multiple fields which can be repeated multiple times.

**Structure:**

```yaml
type: array
schema:
    - name: ...
      type: ...
```

## Examples

### **Simple header**

```yaml
- name: topbar_links
  type: menu
- name: phones
  type: phones
- name: email
  type: email
- name: slogan
- name: socials
  type: socials
- name: menu
  type: menu
```

### **Advantages**

```yaml
- name: advantages
  type: array
  schema:
      - name: icon
        type: image
      - name: title
      - name: description
```

### **Services**

```yaml
- name: services
  type: array
  schema:
      - name: icon
        type: image
      - name: title
      - name: description
      - name: link
        type: page
```

### **Cta**

```yaml
- name: button
- name: form
  type: form
```

### **Banner**

```yaml
- name: image
  type: image
- name: title
- name: description
- name: button
- name: link
  type: page
```
