# LMS-BE


Whenever you deploy it on the server like NGINX please add the following to your NGINX config
to enable static file access without any issue of CORS

```
location / {
	try_files $uri $uri/ /index.php$is_args$query_string;
	location ~ \.(bmp|cur|gif|ico|jpe?g|png|svgz?|webp|pdf)$ {
    add_header Access-Control-Allow-Origin *;
  }
}
```

