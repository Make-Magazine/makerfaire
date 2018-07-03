function shuffle(array) {
    let counter = array.length;
    while (counter > 0) {
        let index = Math.floor(Math.random() * counter);
        counter--;
        let temp = array[counter];
        array[counter] = array[index];
        array[index] = temp;
    }
    return array;
}
    
function billboard(element, options) {
    var defaults = {
            messages: [],
            interval: 3000
        },
        plugin = this,
        currentIndex = 0,
        $element = jQuery(element);
    plugin.settings = {};
    var displayNext = function () {
        clearTimeout(plugin.timerId);
        if (currentIndex >= plugin.settings.messages.length - 1) {
            currentIndex = 0;
        } else {
            currentIndex++;
        }
        jQuery(element).fadeOut("fast", function () {
            jQuery(element).text(plugin.settings.messages[currentIndex]);
        });
        jQuery(element).fadeIn("fast");
        plugin.timerId = setTimeout(displayNext, plugin.settings.interval);
    };
    var start = function () {
        plugin.timerId = setTimeout(displayNext, 200);
    };
    plugin.init = function () {
        plugin.settings = jQuery.extend({}, defaults, options);
        start();
    };
    plugin.init();
};

jQuery.fn.billboard = function (options) {
    return this.each(function () {
        if (undefined == jQuery(this).data('billboard')) {
            var plugin = new billboard(this, options);
            jQuery(this).data('billboard', plugin);
        }
    });
};
    
var brief_messages = [
      'Looking for Makey.',
      'Plugging in the router.',
      'Someone tripped over the power cord.',
      'Searching for the cookies.',
      'Is it Faire time yet?',
      'Updating the countdown clock.',
      'Looking for the lost drones.',
      'Adding neon to the tubes.',
      'Greeting our speakers.',
      'Checking the mics.',
      'Thanking our sponsors.',
      'Looking at projects on Maker Share.',
      'Reading Make: magazine.',
      'Thanking our Make: members.',
      'Are we there yet?',
      'I\'m still working on it.'
    ];

jQuery('.billboard').ready(function(){
    jQuery('.billboard').billboard({
        messages: shuffle(brief_messages),
    });
});