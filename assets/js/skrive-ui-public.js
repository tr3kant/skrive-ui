/**
 * Skrive UI - v0.0.0 - 2016-11-01
 * http://webdevstudios.com
 *
 * Copyright (c) 2016;
 * Licensed GPLv2+
 */

(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

/**
 * Skrive UI
 * http://webdevstudios.com
 *
 * Licensed under the GPLv2+ license.
 */

window.SkriveUI = window.SkriveUI || {};

(function (window, document, $, plugin) {
	$(document).ready(function () {
		var singlepost = $('body').hasClass('single-post'),
		    $paragraph = $('.entry-content').find('p'),
		    $headline = $('.entry-content').find('h1,h2,h3,h4,h5'),
		    $list = $('.entry-content').find('ul,dl');

		if (singlepost) {
			$paragraph.wrap('<div class="article-block article-block--content"><div class="field--body block-text"></div></div>');
			$headline.wrap('<div class="article-block article-block--content"><div class="field--headline block-text"></div></div>');
			$list.wrap('<div class="article-block article-block--content"><div class="field--list block-text"></div></div>');
		}
	});
	$(plugin.init);
})(window, document, jQuery, window.SkriveUI);

},{}]},{},[1]);
