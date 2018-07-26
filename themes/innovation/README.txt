From Drupal 8.4.x using Jquery 3.0 cause by some issue js
=> Fixed inv-sticky.js by replacing:
$(window).load(function() by:
$(window).on('load', function()