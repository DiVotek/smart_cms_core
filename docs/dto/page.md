## PageDto

### Description

The `PageDto` is used to represent page information throughout the application.

### Properties

| Property     | Type     | Description                                                                         |
| ------------ | -------- | ----------------------------------------------------------------------------------- |
| `id`         | `int`    | The unique identifier of the page.                                                  |
| `name`       | `string` | The name of the page. Returns multilanguage name if presented.                      |
| `heading`    | `string` | The email address of the user.                                                      |
| `link`       | `string` | Full link to page.                                                                  |
| `image`      | `string` | Image of page which fill inside admin panel. If not presented returns no-image.webp |
| `custom`     | `array`  | A list of custom fields, which will takes from menu section if presented.           |
| `summary`    | `string` | Seo summary of page.                                                                |
| `created_at` | `string` | Date of creation.                                                                   |
| `updated_at` | `string` | Date of last update.                                                                |

Extra values can be passed inside application or extensions by event `cms.page.transform`. More about events can be found [here](../events.md).

### Example

```json
{
    "id": 1,
    "name": "Home",
    "heading": "Welcome to our website",
    "link": "/",
    "image": "https://example.com/image.jpg",
    "custom": [],
    "summary": "This is a summary",
    "created_at": "2021-01-01 00:00:00",
    "updated_at": "2021-01-01 00:00:00"
}
```
