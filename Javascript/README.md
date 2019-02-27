# Class to simple embed a PDF for a web page

I was looking for a way to embed a PDF without that many controls the browser has. The solution is from Mozilla itself, i just wrapped it in a class.
To use it, just include the .js file and:

``` 
var pdf = new handlePdf(
	{
		'wrapper_element': 'the-canvas-id',
		'url': 'http://url-of-pdf-output'
	}
);

pdf.init();
````

