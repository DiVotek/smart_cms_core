## PageDto

### Description

The `PageEntityDto` is used to represent a set of current page information. It is available on all pages in their layouts if the layout is not modified by extensions.

### Properties

| Property      | Type     | Description                                                                          |
| ------------- | -------- | ------------------------------------------------------------------------------------ |
| `name`        | `string` | The name of the page. Returns multilanguage name if presented.                       |
| `breadcrumbs` | `array`  | A list of breadcrumbs.                                                               |
| `heading`     | `string` | The email address of the user.                                                       |
| `summary`     | `string` | Seo summary of page.                                                                 |
| `content`     | `string` | The content of the page.                                                             |
| `image`       | `string` | Image of page which fill inside admin panel. If not presented returns no-image.webp  |
| `banner`      | `string` | Banner of page which fill inside admin panel. If not presented returns no-image.webp |
| `categories`  | `array`  | A list of categories if page is menu section.                                        |
| `items`       | `array`  | A list of items if page is menu section.                                             |
| `created_at`  | `string` | Date of creation.                                                                    |
| `updated_at`  | `string` | Date of last update.                                                                 |

Extra values can be passed inside application or extensions by event `cms.page-entity.transform`. More about events can be found [here](../events.md).

### Example

```json
{
    "name": "Home",
    "breadcrumbs": [
        {
            "name": "Home",
            "link": "/"
        }
    ],
    "heading": "Welcome to our website",
    "summary": "This is a summary",
    "content": "<p>Welcome to our website</p>",
    "image": "https://example.com/image.jpg",
    "banner": "https://example.com/banner.jpg",
    "categories": [],
    "items": [],
    "created_at": "2021-01-01 00:00:00",
    "updated_at": "2021-01-01 00:00:00"
}
```
