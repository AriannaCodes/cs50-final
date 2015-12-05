/**
 * scripts.js
 *
 * Computer Science 50
 * Problem Set 7
 *
 * Global JavaScript, if any.
 */
 
 function toggleBox(image, divname)
 {
    $(divname).prop('checked', !$(divname).is(':checked'));
    $(image).toggleClass('checked')
 }
