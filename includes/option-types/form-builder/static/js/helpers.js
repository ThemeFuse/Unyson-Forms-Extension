var fwFormBuilder = {};

/**
 * @param {String} [prefix]
 */
fwFormBuilder.uniqueShortcode = function(prefix) {
	prefix = prefix || 'shortcode_';

	var shortcode = prefix + fw.randomMD5().substring(0, 7);

	shortcode = shortcode.replace(/-/g, '_');

	return shortcode;
};