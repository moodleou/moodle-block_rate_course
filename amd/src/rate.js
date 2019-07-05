define(['jquery', 'core/ajax', 'core/notification'],
function($, ajax, notification) {

    var RateAction = function(selector, courseid, israted) {
        this._region = $(selector);
        this._courseid = courseid;
        this._israted = israted;

        this._region.find('.star').unbind().on('click', 'img', this._setUserChoice.bind(this));
        this._region.find('#block_rate_course-rerate').on('click', this._rerateCourse.bind(this));
    };

    RateAction.prototype._setUserChoice = function(element) {
        var elem = $(element.target).parent();
        var value = elem.data('value');

        if (value != '') {
            ajax.call([{
                methodname: 'block_rate_course_set_rating',
                args: {
                    courseid: this._courseid,
                    rating: value
                },
                done: function(data) {
                    if (data === true) {
                        this._region.find('#block_rate_course-myrating-area').removeClass('hidden');
                        this._region.find('#block_rate_course-stars-area').addClass('hidden');
                        this._region.find('#block_rate_course-myrating').text(value);
                    }
                    return true;
                }.bind(this),
                fail: notification.exception
            }]);
        }
    };

    RateAction.prototype._rerateCourse = function() {
        this._region.find('#block_rate_course-stars-area').removeClass('hidden');
    };

    return RateAction;
});