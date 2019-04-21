This is the Boston College Libraries's downtime notification 

### Adding notices to your site

To add notices to your site, include *downtime-notices-client.js* client script in your page and invoke the notice-checker:

```javascript
options = {styles:true}
BCLibDowntimeNotices(options);
``` 

Options include:

* string `styles` - Include the application CSS (default: *false*)
* string `class` - The class name to assign the notice `<div>` (default: *downtime-notification*)
* fn `callback` - When passed a callback function, the client will call that function with the notification value as input and not create a notice `<div>`.
* string `url` - Specify the notice server URL (default *https://arc.bc.edu/notices/active*)
