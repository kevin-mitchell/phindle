#Phindle - Kindle eBook Generator#

##What does Phindle do?##

Phindle makes it simpler to create a well formatted kindle friendly mobi ebook. This is done by allowing you to add content to an instance of the Phindle class, and then from there automatically generated the supporting meta/structure related files required by kindlegen.

Assuming you provide the necessary inputs (see below), the mobi book generated will automatically have:

1) A table of contents in the beginning of the book
2) A logic table of contents (this is table of contents accessible via Kindle's menu - not the same thing as the ToC in the beginning of a book)
3) Various metadata populed within the book (title, author, isbn, publish date, plus cover images, the reading order of your content, etc)

##Prerequisites/Requisites##

Unfortunately, the command line utility `kindlegen` is a prerequisite of Phindle. It is not something that can be installed via composer and so I wanted to mention it first before anything else because it needs to be installed manually. `kindlegen` is a tool **provided free by Amazon** for creating mobi ebook files from a variety of different input formats, including most notably (for us) .html files.

[**`kindlegen` can be downloaded here**](http://www.amazon.com/gp/feature.html?docId=1000765211)

You may need to adjust permissions in your environment - Phindle creates a number of temporary files required by the kindlegen tool. This includes a number of html files, an ncx file, and an opf file. You can specify the path that these files are saved to, and they will be automatically deleted after creating of the final ebook.

##Use##

**As of version 0.1.0 the API for Phindle is not yet stable and I plan on doing a refactoring to improve the overall design of the code base. Watch for changes/improvements going forward**

Check out a video walkthrough of the features/use of Phindle below:

[![ScreenShot](http://www.develpr.com/uploads/images/phindle_video_image.jpg)](https://www.youtube.com/watch?v=H-2a8ol7Fjo)


