/**
 *  @class    handlePdf
 *  @title    Class to embed PDF into a web page without pdf controls, just next and previous
 *  @author   Marco A. Simao
 *  @company  AgoraEntertraining
 * 
 *  Based and uses pdfjs from Mozilla : 
 * 
 * Pre-requisites: 
 *    HTML elements: 
 *      2 buttons = { 'next', 'prev'  } - Pre defined id (next, prev)
 *      1 pages container (current_page / total_pages) - Pre defined id (page_num)
 */

class handlePdf {
  url             = null;
  wrapper_element = null;
  pdfDoc          = null;
  pageNum         = 1;
  pageRendering   = false;
  pageNumPending  = null;
  scale           = 0.8;
  canvas          = null;
  ctx             = null;
  pdfjsLib        = null;
  parent          = this;

  /**
   * 
   * @param options { url-of-pdf-output, wrapper_element that is the string with element's id } 
   */
  constructor(options) {
    this.url = options.url;
    this.wrapper_element = options.wrapper_element;

    this.canvas = document.getElementById(this.wrapper_element),
    this.ctx = this.canvas.getContext('2d');
  }

  /*
    // If absolute URL from the remote server is provided, configure the CORS
    // header on that server.
  */

  /**
   * Get page info from document, resize canvas accordingly, and render page.
   * @param num Page number.
   */
  renderPage(num) {
    this.pageRendering = true;
    // Using promise to fetch the page
    var that = this;

    this.pdfDoc.getPage(num).then(function(page) {
      var viewport = page.getViewport({scale: that.scale});
      that.canvas.height = viewport.height;
      that.canvas.width = viewport.width;

      // Render PDF page into canvas context
      var renderContext = {
        canvasContext: that.ctx,
        viewport: viewport
      };
      var renderTask = page.render(renderContext);

      // Wait for rendering to finish
      renderTask.promise.then(function() {
        that.pageRendering = false;
        if (that.pageNumPending !== null) {
          // New page rendering is pending
          that.renderPage(that.pageNumPending);
          that.pageNumPending = null;
        }
      });
    });

    // Update page counters
    document.getElementById('page_num').textContent = num;
  }

  /**
   * If another page rendering in progress, waits until the rendering is
   * finised. Otherwise, executes rendering immediately.
   */
  queueRenderPage(num) {
    if (this.pageRendering) {
      this.pageNumPending = num;
    } else {
      this.renderPage(num);
    }
  }

  /**
   * Displays previous page.
   */
  onPrevPage = () => {
    if (this.pageNum <= 1) {
      return;
    }
    this.pageNum--;
    this.queueRenderPage(this.pageNum);
  }
  

  /**
   * Displays next page.
   */
  onNextPage = () => {
    if (this.pageNum >= this.pdfDoc.numPages) {
      return;
    }
    this.pageNum++;
    this.queueRenderPage(this.pageNum);
  }

  init() {
    /**
     * Asynchronously downloads PDF.
     */

    var head= document.getElementsByTagName('head')[0];
    var script= document.createElement('script');
    var that = this;

    script.type= 'text/javascript';
    script.src= '//mozilla.github.io/pdf.js/build/pdf.js'; // after finishing, make this js local
    script.onload = function () {
      that.pdfjsLib = window['pdfjs-dist/build/pdf'];

      // The workerSrc property shall be specified.
      that.pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

      that.pdfjsLib.getDocument(that.url).promise.then(function(pdfDoc_) {
        that.pdfDoc = pdfDoc_;
        document.getElementById('page_count').textContent = that.pdfDoc.numPages;
  
        // Initial/first page rendering
        that.renderPage(that.pageNum);

        document.getElementById('prev').addEventListener('click', that.onPrevPage);
        document.getElementById('next').addEventListener('click', that.onNextPage);
      });
    };
    head.appendChild(script);
  }
}
