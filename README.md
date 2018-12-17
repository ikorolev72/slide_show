# This script parse the file in csv format and doing video file with parameters from this file

##  What is it?
##  -----------
Command line script parse the file in csv format and doing video file with parameters from this file.
This script use image2video API for video creation. 


##  The Latest Version

	version 1.5 2018.12.17

##  What's new

	version 1.5 2018.12.17
	
	*	Added bulk settings in csv file


### How to run
```
	Usage: phpcsv_processing.php --csv file.csv [--csv file1.csv [--csv file2.csv...]] [--logo http://logo] [--audio http://audio] [--splash http://splash] [--url http://example.com/image2video] [--duration_caption 5] [--duration_caption 12] [--debug]
	where:
	--csv file.csv - csv file with data
	--logo http://logo - url of logo image
	--audio http://audio - url of audio file
	--splash http://splash - url of splash image
  --url http://example.com - main url of your site
  --duration_caption - duraton for Caption ( default 4 sec )
  --duration_subcaption - duraton for subCaption ( default 8 sec )
  --debug  show additional debug info


	Example: $script --csv data.csv --logo http://localhost/logo.png --url http://example.com --duration_caption 5 --duration_subcaption 10
```

### CSV format

Bellow is the sample of csv file. It contain two parts:

#### Top part of csv
  + title ( or "Title" ) - this text will be shown in first 6 second of video
  + main_url (or "Main image") - this image will be shown in first 6 second of video.
  + audio_url - [optional] audio stream. Supported aac and mp3. Overides the both default and command line argument for audio
  + logo_url - [optional] this image will be resized and shown in the top left corner of video. Overides the both default and command line argument for logo
  + splash_url - [optional] this image will be shown in the end of video. Overides the both default and command line argument for splash
  + duration - [optional] default values ( 4 and 8 ) for Capture and Subcapture. Overides the command line arguments `--duration_caption` and `--duration_subcaption`. Can be be overiden with `duration_auto` csv value. 
  + duration_auto - [optional] default values for Capture and Subcapture. Overiden values of  `duration`
  + font - [optional] default fonts for Capture and Subcapture
  + font_size - [optional] default font_size for Capture and Subcapture
  + text_color - [optional] default color for Capture and Subcapture
  + text_boxborder_color - [optional] default box color for Capture and Subcapture
  + text_boxopacity - [optional] default box opacity for Capture and Subcapture
  + crop_image - [optional] If set to 1, then resize image to the minimum size that will completely fill the video, then crop image (removes invisible parts). If set to 0, then simple resize so the image fits completely and fill  fills the remaining places with black
  + text_effect - [optional] default text effect for Capture and Subcapture. Values: default, zoom_in, zoom_out, scrolling_bottom_to_top, stop_line_top, stop_line_bottom, stop_line_left, stop_line_right
  + text_align - [optional] default vertical align for Capture and Subcapture. Values: top, center, bottom
  + additional_text - [optional] Text in top right corner of video. Used only if column `Unit image source 1` in second part of csv-file is empty.

#### Bottom  part of csv
  + Unit caption 1 - Caption text
  + Unit caption 2 - Do not used
  + Unit subcaption 1 - SubCaption text
  + Unit subcaption 2 - Do not used
  + Unit image 1 - Url of image
  + Unit image source 1 - Additional text in top right corner of video
  + Unit image source url 1 - Do not used
  + Unit image 2 - Do not used
  + Unit image source 2 - Do not used
  + Unit image source url  - Do not used


Example of csv-file:
```
title,How To Not Be A Jerk During The Holidays ,,,,,,,,
main_url,https://proxy.example.com/ipicimg/5SJCO00P6S9BHIN3-cp0x0x2000x1001,,,,,,,,
audio_url,http://www.example.com/image2video/uploads/1541601782d3fa63b793871f791c8db512207f4312b716b389/b36dc81055cb51f52d71c198613cb811304dc971.mp3,,,,,,,,
logo_url,http://example.com/image2video/uploads/15417092127723a243f0e122a400c54eaf6a9bda7c65095387/f92aa8e16e80d7d8e02c9c6a0b33948478650483.png,,,,,,,,
splash_url,http://example.com/image2video/uploads/15417092127723a243f0e122a400c54eaf6a9bda7c65095387/11a98349fb9e66f12a7a9cd0255ea9822d85ea5a.jpeg,,,,,,,,
duration,4,8,,,,,,,
duration_auto,4,4,,,,,,,
font,/usr/share/fonts/truetype/roboto/hinted/Roboto-Bold.ttf,/usr/share/fonts/truetype/roboto/hinted/Roboto-Bold.ttf,,,,,,,
font_size,50,40,,,,,,,
text_color,000000,FC56BC,,,,,,,
text_boxborder_color,FFFFFF,EEEEEE,,,,,,,
text_boxopacity,50,40,,,,,,,
crop_image,0,0,,,,,,,
text_effect,default,zoom_out,,,,,,,
text_align,center,bottom,,,,,,,
additional_text,Any text,Any text,,,,,,,
,,,,,,,,,
Unit caption 1,Unit caption 2,Unit subcaption 1,Unit subcaption 2,Unit image 1,Unit image source 1,Unit image source url 1,Unit image 2,Unit image source 2,Unit image source url 2
Don't hang your Christmas decorations too early.,,"Like, Independence Day is probably aggressive.",,https://proxy.example.com/ipicimg/UCJI4RU51RJI9CEE,Alan Quinonez ,,,,
Make sure to have some extra gifts on retainer.,,What if Second Cousin Bertha shows up with socks?,,https://proxy.example.com/ipicimg/N2O3M9NIRJN3AM78,Alan Quinonez ,,,,
Don't cut up food for your adult child.,,No one needs to see a grown man play Here Comes the Airplane.,,https://proxy.example.com/ipicimg/13J818FOEL18M6SF,Alan Quinonez ,,,,

```

##  Bugs
##  ------------


  Licensing
  ---------
	GNU

  Contacts
  --------

     o korolev-ia [at] yandex.ru
     o http://www.unixpin.com

