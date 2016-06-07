(function( $ ) {
	'use strict';

	$(document).ready(function(){

		/**
		 * Number.prototype.format(n, x)
		 *
		 * @param integer n: length of decimal
		 * @param integer x: length of sections
		 */
		Number.prototype.format = function(n, x) {
		    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
		};

		$('.defm-datepicker').datepicker();
		$('#defm_admin_tabs').tabs({
			heightStyle: "content",
			hide: true,
			show: true,
		});

		/**
		 *
		 * Manual export tab
		 *
		 */


		$('#defm_week').change(function(event) {
			if ($(this).val() === "1") {
				$('#defm_date_range').show();
			} else {
				$('#defm_date_range').hide();
			}
		});

		$('#defm_group_week').change(function(event) {
			if ($(this).val() === "1") {
				$('#defm_group_date_range').show();
			} else {
				$('#defm_group_date_range').hide();
			}
		});

		decomExport.$notice = $('#defm_notice');
		decomExport.isNoticeVisible = false;

		$('#defm_submit').click(function(event) {
			event.preventDefault();

			$('#defm_options_form').find('input, select').prop("disabled", true);
			decomExport.appendElement('<h3 class="defm-working">Generating report...</h3>');
			decomExport.formData = {
				product: $('#defm_product').val(),
				week: $('#defm_week').val(),
				start_date: $('#defm_start_date').val(),
				end_date: $('#defm_end_date').val(),
				filename: $('#defm_filename').val()
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_generate_report_start',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: decomExport.generateReportStart
			});
		});

		decomExport.generateReportStart = function (response) {
			if (response.status === 'success') {
				decomExport.userCount = response.userCount;
				decomExport.appendElement('<div class="defm-status-line">Total number of users: ' + decomExport.userCount.format(0, 3) + '</div>');
				if (decomExport.userCount) {
					$.ajax(decomExport.ajaxUrl, {
						type: 'POST',
						data: {
							action: 'defm_generate_report_continue',
							security: response.security,
							form_data: decomExport.formData
						},
						success: decomExport.generateReportContinue
					});
				} else {
					decomExport.appendElement('<div class="defm-status-line">There is no login data to process.</div>');
					$('.defm-working').addClass('finished').text('No login data to export.');
					$('#defm_options_form').find('input, select').prop("disabled", false);
				}
			} else {
				decomExport.generateReportError(response);
			}
		};

		decomExport.generateReportContinue = function (response) {
			if (response.status === 'success') {
				decomExport.entriesCount = response.entriesCount;
				decomExport.appendElement('<div class="defm-status-line">Total number of logins in database to process: ' + decomExport.entriesCount.format(0, 3) + '</div>');
				decomExport.$progressElement = decomExport.appendElement('<div id="defm_progress" class="defm-progress"></div>');
				decomExport.$progressElement.progressbar({
					max: decomExport.userCount
				});
				decomExport.$progressElement.progressbar({
					value: 749
				});
				$.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_generate_report_step',
						security: response.security,
						form_data: decomExport.formData,
						user_count: decomExport.userCount,
						entries_count: decomExport.entriesCount,
						start: 0
					},
					success: decomExport.generateReportStep
				});
			} else {
				decomExport.generateReportError(response);
			}
		};

		decomExport.generateReportStep = function (response) {
			if (response.status === 'progress') {
				decomExport.generateReportProgress(response);

				$.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_generate_report_step',
						security: response.security,
						file: response.file,
						form_data: decomExport.formData,
						user_count: decomExport.userCount,
						entries_count: decomExport.entriesCount,
						start: response.progress
					},
					success: decomExport.generateReportStep
				})
			} else if (response.status === 'success') {
				decomExport.generateReportFinish(response);
			} else {
				decomExport.generateReportError(response);
			}
		};

		decomExport.generateReportProgress = function (response) {
			decomExport.$progressElement.progressbar({
				value: response.progress < decomExport.userCount ? (decomExport.$progressElement.progressbar('value') + response.progress) : Math.floor(decomExport.userCount * 0.95)
			});
		};

		decomExport.generateReportFinish = function (response) {
			if (response.status === 'success') {
				decomExport.$progressElement.addClass('complete').progressbar({value: decomExport.userCount+1});

				$.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_generate_report_finish',
						security: response.security,
						file: response.file,
						form_data: decomExport.formData
					},
					success: function(response) {
						if (response.status === 'success') {
							decomExport.appendElement('<div class="defm-download-button"><a href="' + response.reportUrl + '" class="defm-download-link button button-download defm-button dashicons-before dashicons-download">Download report</a></div>');
							$('#defm_no_history_reports').slideUp(100, function() {
								$('#defm_no_history_reports').remove();
							});
							decomExport.appendElement(response.reportElement, decomExport.$historyList);
							$('.defm-working').addClass('finished').text('Your report has been generated');
							$('#defm_options_form').find('input, select').prop("disabled", false);
						} else {
							decomExport.generateReportError(response);
						}
					}
				});
			} else {
				decomExport.generateReportError(response);
			}
		};

		decomExport.generateReportError = function (response) {
			decomExport.$progressElement.addClass('error').progressbar({
				value: Math.floor(decomExport.campaigns * 0.5)
			})
			$('.defm-working:not(.finished)').addClass('error').text('Something went wrong. Please try again.');
			decomExport.$notice.addClass('error').removeClass('updated');
			var error = response.error ? response.error : response;
			decomExport.appendElement('<div class="defm-status-line error">Error: ' + error + '</div>');
			$('#defm_options_form, #defm_groups_form').find('input, select').prop("disabled", false);
			console.error('Export for MemberPress: ' + error);
			console.log(error);

		};

		decomExport.appendElement = function(element, $parent) {
			var $element = $($.parseHTML(element));
			$element.css({opacity: 0, display: 'none'});
			var $appendTo = $parent || decomExport.$notice;
			$appendTo.append($element);
			$element.slideDown(200, function(){
				$element.animate({ opacity: 1}, 200, function() {
					$element.addClass('new');
					setTimeout(function() {
						$element.removeClass('new');
					}, 3000);
					// refresh history filter list
					decomExport.$historyFilterType.change();
				});
			});
			if (!$appendTo.is(':visible')) {
				$appendTo.slideDown(200, function() {
					$appendTo.animate({opacity: 1});
				});
			}
			return $element;
		}

		decomExport.prependElement = function(element, $parent) {
			var $element = $($.parseHTML(element));
			$element.css({opacity: 0, display: 'none'});
			var $prependTo = $parent || decomExport.$notice;
			$prependTo.prepend($element);
			$element.slideDown(200, function(){
				$element.animate({ opacity: 1}, 200, function() {
					$element.addClass('new');
					setTimeout(function() {
						$element.removeClass('new');
					}, 3000);
				});
			});
			if (!$prependTo.is(':visible')) {
				$prependTo.slideDown(200, function() {
					$prependTo.animate({opacity: 1});
				});
			}
			return $element;
		}

		decomExport.removeErrors = function() {
			decomExport.removeElement($('.defm-status-line.error'), 100);
			decomExport.$notice.removeClass('error');
		}

		decomExport.showErrors = function() {
			var posi = $('#defm_notice').offset().top;
			 // define a new scroll function with an additional parameter
			$("html, body").animate({scrollTop: posi});
		}

		/**
		 *
		 * Scheduled reports tab
		 *
		 */

		 $('#defm_schedule').change(function(event) {
		 	if ($(this).val() === "week") {
		 		$('#defm_schedule_weekday_wrapper').show();
		 	} else {
		 		$('#defm_schedule_weekday_wrapper').hide();
		 	}
		 });

		decomExport.$addScheduledButton = $('#defm_add_scheduled_report');
		decomExport.$addScheduledForm = $('#defm_scheduled_report_form_wrapper');
		decomExport.$scheduledReportList = $('#defm_scheduled_reports');

		decomExport.addScheduledReport = function(event) {
			$(this).prop('disabled', true);
			decomExport.$addScheduledForm.slideDown(200, function(){
				decomExport.$addScheduledForm.animate({ opacity: 1});
				decomExport.$addScheduledForm.find('#defm_scheduled_report_name').focus();
			});
		};

		decomExport.$addScheduledButton.on('click', decomExport.addScheduledReport);

		$('#defm_scheduled_footer').on('click', '#defm_scheduled_submit:not(.edit)', function(event) {
			event.preventDefault();

			decomExport.$addScheduledForm.find('input, select').prop("disabled", true);
			$('#defm_scheduled_submit_wrapper').addClass('defm-working');
			decomExport.formData = {
				report_name: $('#defm_scheduled_report_name').val(),
				product: $('#defm_scheduled_product').val(),
				schedule: $('#defm_schedule').val(),
				weekday: $('#defm_schedule').val() === 'week' ? $('#defm_schedule_weekday').val() : false,
				filename: $('#defm_scheduled_filename').val(),
				ftp_profile: $('#defm_schedule_ftp_profile').val(),
				email_profile: $('#defm_schedule_email_profile').val(),
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_add_scheduled_report',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: decomExport.displayNewScheduledReport,
				error: decomExport.displayNewScheduledError
			});
		});

		$('#defm_scheduled_footer').on('click', '#defm_scheduled_cancel:not(.edit)', function(event) {
			event.preventDefault();

			decomExport.$addScheduledForm.animate({ opacity: 0 }, 200, function() {
				decomExport.$addScheduledForm.slideUp(200, function() {
					decomExport.$addScheduledButton.prop('disabled', false);
				});
			});

		});

		decomExport.displayNewScheduledReport = function (response) {
			if (response.status === 'success') {
				$('#defm_no_jobs').slideUp(100, function() {
					$('#defm_no_jobs').remove();
				});

				decomExport.appendElement(response.element, decomExport.$scheduledReportList);
				decomExport.appendElement('<div class="defm-status-line"><strong>Saved scheduled report:</strong> ' + response.jobTitle + '</div>');
				decomExport.removeErrors();

				decomExport.$addScheduledForm.animate({ opacity: 0 }, 200, function() {
					decomExport.$addScheduledForm.slideUp(200, function() {
						$('#defm_scheduled_submit_wrapper').removeClass('defm-working');
						decomExport.$addScheduledForm.find('input, select').prop("disabled", false);
						decomExport.$addScheduledButton.prop('disabled', false);

					});
				});
			} else {
				decomExport.displayNewScheduledError(response);
			}
		};

		decomExport.displayNewScheduledError = function (response) {
			var error = response.error || response.responseText || response;
			decomExport.$notice.addClass('error');
			decomExport.appendElement('<div class="defm-status-line error">Error: ' + error + '</div>');
			$('#defm_scheduled_submit_wrapper').removeClass('defm-working');
			decomExport.$addScheduledForm.find('input, select').prop("disabled", false);
			decomExport.showErrors();
			console.error('Decom Import for MemberPress: ' + error);
			console.log(error);
		};


		decomExport.$scheduledReportList.on('click', '.defm-edit', function (event) {
			event.preventDefault();
			decomExport.$addScheduledButton.prop('disabled', true);

			var jobID = parseInt($(this).data('decom-job'));
			var $jobElement = $(document.getElementById('defm_job_' + jobID));
			var jobTitle = $jobElement.find('.defm-job-title').text();

			decomExport.$scheduledReportList.find('.defm-job-buttons button').prop('disabled', true);

			decomExport.defaultScheduledFormData = {
				report_name: $('#defm_scheduled_report_name').val(),
				product: $('#defm_scheduled_product').val(),
				schedule: $('#defm_schedule').val(),
				weekday: $('#defm_schedule').val() === 'week' ? $('#defm_schedule_weekday').val() : false,
				filename: $('#defm_scheduled_filename').val(),
				ftp_profile: $('#defm_schedule_ftp_profile').val(),
				email_profile: $('#defm_schedule_email_profile').val(),
			};

			$('#defm_scheduled_submit, #defm_scheduled_cancel').addClass('edit').data('decom-job', jobID);
			$('#defm_scheduled_report_name').val(jobTitle);
			$('#defm_scheduled_product').val($jobElement.find('.defm-job-details-product').data('decom-product'));
			$('#defm_schedule').val($jobElement.find('.defm-job-details-schedule').data('decom-schedule')).change();
			$('#defm_weekday').val($jobElement.find('.defm-job-details-schedule').data('decom-weekday'));
			$('#defm_scheduled_filename').val($jobElement.find('.defm-job-details-filename').data('decom-filename'));
			$('#defm_schedule_ftp_profile').val($jobElement.find('.defm-job-details-ftp-profile').data('decom-ftp-profile'));
			$('#defm_schedule_email_profile').val($jobElement.find('.defm-job-details-email-profile').data('decom-email-profile'));

			$jobElement.find('.defm-job-preview, .defm-job-edit').animate({opacity: 0}, 200, function() {
				$jobElement.addClass('edit');
				decomExport.$addScheduledForm.appendTo($jobElement).css({display: 'block'}).animate({opacity: 1}, 200);
				decomExport.$addScheduledForm.find('#defm_scheduled_report_name').focus();
			});
		});


		decomExport.$scheduledReportList.on('click', '#defm_scheduled_submit.edit', function(event) {
			event.preventDefault();

			decomExport.$addScheduledForm.find('input, select').prop("disabled", true);
			$('#defm_scheduled_submit_wrapper').addClass('defm-working');
			var jobID = parseInt($(this).data('decom-job'));
			var $jobElement = $(document.getElementById('defm_job_' + jobID));

			decomExport.formData = {
				job_id: jobID,
				report_name: $('#defm_scheduled_report_name').val(),
				product: $('#defm_scheduled_product').val(),
				schedule: $('#defm_schedule').val(),
				weekday: $('#defm_schedule').val() === 'week' ? $('#defm_schedule_weekday').val() : false,
				filename: $('#defm_scheduled_filename').val(),
				ftp_profile: $('#defm_schedule_ftp_profile').val(),
				email_profile: $('#defm_schedule_email_profile').val(),
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_update_scheduled_report',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: function(response) {
					if (response.status === 'success') {
						$jobElement.find('.defm-job-title').text(response.jobTitle);
						$jobElement.find('.defm-job-preview').remove();
						$jobElement.prepend($(response.element).find('.defm-job-preview'));
						decomExport.appendElement('<div class="defm-status-line"><strong>Updated Scheduled report:</strong> ' + response.jobTitle + '</div>');
						decomExport.removeErrors();
						$('#defm_scheduled_submit_wrapper').removeClass('defm-working');
						decomExport.$addScheduledForm.find('input, select').prop("disabled", false);

						$jobElement.find('#defm_scheduled_cancel').click();

					} else {
						decomExport.displayNewScheduledError(response);
					}
				},
				error: decomExport.displayNewScheduledError
			});
		});

		decomExport.$scheduledReportList.on('click', '#defm_scheduled_cancel.edit', function(event) {
			event.preventDefault();
			var jobID = parseInt($(this).data('decom-job'));
			var $jobElement = $(document.getElementById('defm_job_' + jobID));


			decomExport.$addScheduledForm.fadeOut(100, function() {
				$jobElement.removeClass('edit');
				$jobElement.find('.defm-job-preview, .defm-job-edit').animate({opacity: 1}, 200);
				decomExport.$scheduledReportList.find('.defm-job-buttons button').prop('disabled', false);
				$('#defm_scheduled_report_name').val(decomExport.defaultScheduledFormData.report_name);
				$('#defm_scheduled_product').val(decomExport.defaultScheduledFormData.product);
				$('#defm_schedule').val(decomExport.defaultScheduledFormData.schedule);
				$('#defm_weekday').val(decomExport.defaultScheduledFormData.weekday);
				$('#defm_scheduled_filename').val(decomExport.defaultScheduledFormData.filename);
				$('#defm_schedule_ftp_profile').val(decomExport.defaultScheduledFormData.ftp_profile);
				$('#defm_schedule_email_profile').val(decomExport.defaultScheduledFormData.email_profile);
				$('#defm_scheduled_submit, #defm_scheduled_cancel').removeClass('edit').data('decom-job', 0);
				decomExport.$addScheduledForm.prependTo('#defm_scheduled_footer');
				decomExport.$addScheduledButton.prop('disabled', false);
			});



		});

		decomExport.$scheduledReportList.on('click', '.defm-delete', function (event) {
			event.preventDefault();
			var jobID = parseInt($(this).data('decom-job'));
			var $jobElement = $(document.getElementById('defm_job_' + jobID));
			var jobTitle = $jobElement.find('.defm-job-title').text();
			if (confirm('Delete ' + jobTitle + '?')) {
				$jobElement.find('.defm-job-buttons').addClass('defm-working');
				$jobElement.find('.defm-job-buttons button').prop('disabled', true);
				decomExport.jobData = {
					job_id: jobID
				};
				var jqxhr = $.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_delete_scheduled_report',
						security: decomExport.security,
						form_data: decomExport.jobData
					},
					success: function (response) {
						if (response.status === 'success') {
							$jobElement.find('.defm-job-buttons').removeClass('defm-working');
							decomExport.removeElement($jobElement);
							decomExport.removeErrors();
							decomExport.appendElement('<div class="defm-status-line"><strong>Deleted scheduled report:</strong> ' + jobTitle + '</div>');
						} else {
							decomExport.displayNewScheduledError(response);
						}
					},
					error: decomExport.displayNewScheduledError
				});
			}
		});

		decomExport.$scheduledReportList.on('click', '.defm-toggle', function (event) {
			event.preventDefault();
			var $toggleButton = $(this);
			var jobID = parseInt($toggleButton.data('decom-job'));
			var $jobElement = $(document.getElementById('defm_job_' + jobID));
			var jobTitle = $jobElement.find('.defm-job-title').text();
			$jobElement.find('.defm-job-buttons').addClass('defm-working');
			$jobElement.find('.defm-job-buttons button').prop('disabled', true);
			decomExport.jobData = {
				job_id: jobID
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_toggle_scheduled_report_status',
					security: decomExport.security,
					form_data: decomExport.jobData
				},
				success: function (response) {
					if (response.status === 'success') {
						$toggleButton.toggleClass('dashicons-controls-pause dashicons-controls-play');
						$toggleButton.text(response.jobStatus === 'disabled' ? 'Enable' : 'Disable');
						$jobElement.find('.defm-job-description').html(response.jobSchedule);
						$jobElement.find('.defm-job-buttons').removeClass('defm-working');
						$jobElement.find('.defm-job-buttons button').prop('disabled', false);
						$jobElement.toggleClass('disabled');
						decomExport.removeErrors();
						decomExport.appendElement('<div class="defm-status-line"><strong>Scheduled report ' + response.jobStatus + ':</strong> ' + jobTitle + '</div>');
					} else {
						decomExport.displayNewScheduledError(response);
					}
				},
				error: decomExport.displayNewScheduledError
			});
		});

		decomExport.removeElement = function ($element, timeout) {
			timeout = timeout || 2000;
			$element.addClass('deleted');
			setTimeout(function () {
				$element.animate({ opacity: 0 }, 200, function() {
					$element.slideUp(200, function() {
						$element.remove();
					});
				});
			}, timeout);
		}

		/**
		 *
		 * Ftp profiles tab
		 *
		 */

		decomExport.$addFtpProfileButton = $('#defm_add_ftp_profile');
		decomExport.$addFtpProfileForm = $('#defm_ftp_profile_form_wrapper');
		decomExport.$ftpProfileList = $('#defm_ftp_profiles');

		decomExport.addFtpProfile = function(event) {
			$(this).prop('disabled', true);
			decomExport.$addFtpProfileForm.slideDown(200, function(){
				decomExport.$addFtpProfileForm.animate({ opacity: 1});
				decomExport.$addFtpProfileForm.find('#defm_ftp_profile_name').focus();
			});
		};

		decomExport.$addFtpProfileButton.on('click', decomExport.addFtpProfile);

		$('#defm_ftp_profile_footer').on('click', '#defm_ftp_profile_submit:not(.edit)', function(event) {
			event.preventDefault();

			decomExport.$addFtpProfileForm.find('input, select').prop("disabled", true);
			$('#defm_ftp_profile_submit_wrapper').addClass('defm-working');
			decomExport.formData = {
				profile_name: $('#defm_ftp_profile_name').val(),
				server: $('#defm_ftp_profile_server').val(),
				username: $('#defm_ftp_profile_username').val(),
				password: $('#defm_ftp_profile_password').val(),
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_add_ftp_profile',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: decomExport.displayNewFtpProfile,
				error: decomExport.displayNewFtpProfileError
			});
		});

		$('#defm_ftp_profile_footer').on('click', '#defm_ftp_profile_cancel:not(.edit)', function(event) {
			event.preventDefault();

			decomExport.$addFtpProfileForm.animate({ opacity: 0 }, 200, function() {
				decomExport.$addFtpProfileForm.slideUp(200, function() {
					decomExport.$addFtpProfileButton.prop('disabled', false);
				});
			});

		});

		decomExport.displayNewFtpProfile = function (response) {
			if (response.status === 'success') {
				$('#defm_no_ftp_profiles').slideUp(100, function() {
					$('#defm_no_ftp_profiles').remove();
				});

				$('#defm_schedule_ftp_profile').append('<option value="' + response.profileID + '">' + response.profileTitle + '</option>');
				decomExport.appendElement(response.element, decomExport.$ftpProfileList);
				decomExport.appendElement('<div class="defm-status-line"><strong>Saved FTP profile:</strong> ' + response.profileTitle + '</div>');
				decomExport.removeErrors();

				decomExport.$addFtpProfileForm.animate({ opacity: 0 }, 200, function() {
					decomExport.$addFtpProfileForm.slideUp(200, function() {
						$('#defm_ftp_profile_submit_wrapper').removeClass('defm-working');
						decomExport.$addFtpProfileForm.find('input, select').prop("disabled", false);
						decomExport.$addFtpProfileButton.prop('disabled', false);

					});
				});
			} else {
				decomExport.displayNewFtpProfileError(response);
			}
		};

		decomExport.displayNewFtpProfileError = function (response) {
			var error = response.error || response.responseText || response;
			decomExport.$notice.addClass('error');
			decomExport.appendElement('<div class="defm-status-line error">Error: ' + error + '</div>');
			$('#defm_ftp_profile_submit_wrapper').removeClass('defm-working');
			decomExport.$addFtpProfileForm.find('input, select').prop("disabled", false);
			decomExport.showErrors();
			console.error('Decom Import for MemberPress: ' + error);
			console.log(error);
		};


		decomExport.$ftpProfileList.on('click', '.defm-edit', function (event) {
			event.preventDefault();
			decomExport.$addFtpProfileButton.prop('disabled', true);

			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));
			var profileTitle = $profileElement.find('.defm-profile-title').text();

			decomExport.$ftpProfileList.find('.defm-profile-buttons button').prop('disabled', true);

			decomExport.defaultFtpProfileFormData = {
				profile_name: $('#defm_ftp_profile_name').val(),
				server: $('#defm_ftp_profile_server').val(),
				username: $('#defm_ftp_profile_username').val(),
				password: $('#defm_ftp_profile_password').val(),
			};

			$('#defm_ftp_profile_submit, #defm_ftp_profile_cancel').addClass('edit').data('decom-profile', profileID);
			$('#defm_ftp_profile_name').val(profileTitle);
			$('#defm_ftp_profile_server').val($profileElement.find('.defm-profile-details-server').data('decom-server'));
			$('#defm_ftp_profile_username').val($profileElement.find('.defm-profile-details-username').data('decom-username'));
			$('#defm_ftp_profile_password').val($profileElement.find('.defm-profile-details-password').data('decom-password'));

			$profileElement.find('.defm-profile-preview, .defm-profile-edit').animate({opacity: 0}, 200, function() {
				$profileElement.addClass('edit');
				decomExport.$addFtpProfileForm.appendTo($profileElement).css({display: 'block'}).animate({opacity: 1}, 200);
				decomExport.$addFtpProfileForm.find('#defm_ftp_profile_name').focus();
			});
		});

		decomExport.$ftpProfileList.on('click', '#defm_ftp_profile_submit.edit', function(event) {
			event.preventDefault();

			decomExport.$addFtpProfileForm.find('input, select').prop("disabled", true);
			$('#defm_ftp_profile_submit_wrapper').addClass('defm-working');
			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));

			decomExport.formData = {
				profile_id: profileID,
				profile_name: $('#defm_ftp_profile_name').val(),
				server: $('#defm_ftp_profile_server').val(),
				username: $('#defm_ftp_profile_username').val(),
				password: $('#defm_ftp_profile_password').val(),
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_add_ftp_profile',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: function(response) {
					if (response.status === 'success') {
						$('#defm_schedule_ftp_profile').find('option[value="' + profileID + '"]').text(response.profileTitle);
						$profileElement.find('.defm-profile-title').text(response.profileTitle);
						$profileElement.find('.defm-profile-preview').remove();
						$profileElement.prepend($(response.element).find('.defm-profile-preview'));
						decomExport.appendElement('<div class="defm-status-line"><strong>Updated FTP profile:</strong> ' + response.profileTitle + '</div>');
						decomExport.removeErrors();
						$('#defm_ftp_profile_submit_wrapper').removeClass('defm-working');
						decomExport.$addFtpProfileForm.find('input, select').prop("disabled", false);

						$profileElement.find('#defm_ftp_profile_cancel').click();

					} else {
						decomExport.displayNewFtpProfileError(response);
					}
				},
				error: decomExport.displayNewFtpProfileError
			});
		});

		decomExport.$ftpProfileList.on('click', '#defm_ftp_profile_cancel.edit', function(event) {
			event.preventDefault();
			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));


			decomExport.$addFtpProfileForm.fadeOut(100, function() {
				$profileElement.removeClass('edit');
				$profileElement.find('.defm-profile-preview, .defm-profile-edit').animate({opacity: 1}, 200);
				decomExport.$ftpProfileList.find('.defm-profile-buttons button').prop('disabled', false);
				$('#defm_ftp_profile_name').val(decomExport.defaultFtpProfileFormData.profile_name);
				$('#defm_ftp_profile_server').val(decomExport.defaultFtpProfileFormData.server);
				$('#defm_ftp_profile_username').val(decomExport.defaultFtpProfileFormData.username);
				$('#defm_ftp_profile_password').val(decomExport.defaultFtpProfileFormData.paassword);
				decomExport.$addFtpProfileForm.prependTo('#defm_ftp_profile_footer');
				decomExport.$addFtpProfileButton.prop('disabled', false);
			});
		});

		decomExport.$ftpProfileList.on('click', '.defm-delete', function (event) {
			event.preventDefault();
			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));
			var profileTitle = $profileElement.find('.defm-profile-title').text();
			if (confirm('Delete ' + profileTitle + '?')) {
				$profileElement.find('.defm-profile-buttons').addClass('defm-working');
				$profileElement.find('.defm-profile-buttons button').prop('disabled', true);
				decomExport.profileData = {
					profile_id: profileID
				};
				var jqxhr = $.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_delete_profile',
						security: decomExport.security,
						form_data: decomExport.profileData
					},
					success: function (response) {
						if (response.status === 'success') {
							$profileElement.find('.defm-profile-buttons').removeClass('defm-working');
							$('#defm_schedule_ftp_profile').find('option[value="' + profileID + '"]').remove();
							decomExport.removeElement($profileElement);
							decomExport.removeErrors();
							decomExport.appendElement('<div class="defm-status-line"><strong>Deleted FTP profile:</strong> ' + profileTitle + '</div>');
						} else {
							decomExport.displayNewFtpProfileError(response);
						}
					},
					error: decomExport.displayNewFtpProfileError
				});
			}
		});

		/**
		 *
		 * Email profiles tab
		 *
		 */

		decomExport.$addEmailProfileButton = $('#defm_add_email_profile');
		decomExport.$addEmailProfileForm = $('#defm_email_profile_form_wrapper');
		decomExport.$emailProfileList = $('#defm_email_profiles');

		decomExport.addEmailProfile = function(event) {
			$(this).prop('disabled', true);
			decomExport.$addEmailProfileForm.slideDown(200, function(){
				decomExport.$addEmailProfileForm.animate({ opacity: 1});
				decomExport.$addEmailProfileForm.find('#defm_email_profile_name').focus();
			});
		};

		decomExport.$addEmailProfileButton.on('click', decomExport.addEmailProfile);

		$('#defm_email_profile_footer').on('click', '#defm_email_profile_submit:not(.edit)', function(event) {
			event.preventDefault();

			decomExport.$addEmailProfileForm.find('input, select').prop("disabled", true);
			$('#defm_email_profile_submit_wrapper').addClass('defm-working');
			decomExport.formData = {
				profile_name: $('#defm_email_profile_name').val(),
				from: $('#defm_email_profile_from').val(),
				to: $('#defm_email_profile_to').val(),
				subject: $('#defm_email_profile_subject').val(),
				cc: $('#defm_email_profile_cc').val(),
				bcc: $('#defm_email_profile_bcc').val(),
				text: $('#defm_email_profile_text').val(),
				attachment: $('#defm_email_profile_attachment').is(':checked') ? 1 : 0,
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_add_email_profile',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: decomExport.displayNewEmailProfile,
				error: decomExport.displayNewEmailProfileError
			});
		});

		$('#defm_email_profile_footer').on('click', '#defm_email_profile_cancel:not(.edit)', function(event) {
			event.preventDefault();

			decomExport.$addEmailProfileForm.animate({ opacity: 0 }, 200, function() {
				decomExport.$addEmailProfileForm.slideUp(200, function() {
					decomExport.$addEmailProfileButton.prop('disabled', false);
				});
			});

		});

		decomExport.displayNewEmailProfile = function (response) {
			if (response.status === 'success') {
				$('#defm_no_email_profiles').slideUp(100, function() {
					$('#defm_no_email_profiles').remove();
				});

				$('#defm_schedule_email_profile').append('<option value="' + response.profileID + '">' + response.profileTitle + '</option>');
				decomExport.appendElement(response.element, decomExport.$emailProfileList);
				decomExport.removeErrors();
				decomExport.appendElement('<div class="defm-status-line"><strong>Saved Email profile:</strong> ' + response.profileTitle + '</div>');

				decomExport.$addEmailProfileForm.animate({ opacity: 0 }, 200, function() {
					decomExport.$addEmailProfileForm.slideUp(200, function() {
						$('#defm_email_profile_submit_wrapper').removeClass('defm-working');
						decomExport.$addEmailProfileForm.find('input, select').prop("disabled", false);
						decomExport.$addEmailProfileButton.prop('disabled', false);

					});
				});
			} else {
				decomExport.displayNewEmailProfileError(response);
			}
		};

		decomExport.displayNewEmailProfileError = function (response) {
			var error = response.error || response.responseText || response;
			decomExport.$notice.addClass('error');
			decomExport.appendElement('<div class="defm-status-line error">Error: ' + error + '</div>');
			$('#defm_email_profile_submit_wrapper').removeClass('defm-working');
			decomExport.$addEmailProfileForm.find('input, select').prop("disabled", false);
			decomExport.showErrors();
			console.error('Decom Import for MemberPress: ' + error);
			console.log(error);
		};

		decomExport.$emailProfileList.on('click', '.defm-edit', function (event) {
			event.preventDefault();
			decomExport.$addEmailProfileButton.prop('disabled', true);

			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));
			var profileTitle = $profileElement.find('.defm-profile-title').text();

			decomExport.$emailProfileList.find('.defm-profile-buttons button').prop('disabled', true);

			decomExport.defaultEmailProfileFormData = {
				profile_name: $('#defm_email_profile_name').val(),
				from: $('#defm_email_profile_from').val(),
				to: $('#defm_email_profile_to').val(),
				subject: $('#defm_email_profile_subject').val(),
				cc: $('#defm_email_profile_cc').val(),
				bcc: $('#defm_email_profile_bcc').val(),
				text: $('#defm_email_profile_text').val(),
				attachment: $('#defm_email_profile_attachment').is(':checked'),
			};

			$('#defm_email_profile_submit, #defm_email_profile_cancel').addClass('edit').data('decom-profile', profileID);
			$('#defm_email_profile_name').val(profileTitle);
			$('#defm_email_profile_from').val($profileElement.find('.defm-profile-details-from').data('decom-from'));
			$('#defm_email_profile_to').val($profileElement.find('.defm-profile-details-to').data('decom-to'));
			$('#defm_email_profile_subject').val($profileElement.find('.defm-profile-details-subject').data('decom-subject'));
			$('#defm_email_profile_cc').val($profileElement.find('.defm-profile-details-cc').data('decom-cc'));
			$('#defm_email_profile_bcc').val($profileElement.find('.defm-profile-details-bcc').data('decom-bcc'));
			$('#defm_email_profile_text').val($profileElement.find('.defm-email-profile-description').data('decom-text'));
			$('#defm_email_profile_attachment').prop('checked', !!parseInt($profileElement.find('.defm-profile-details-attachment').data('decom-attachment')));

			$profileElement.find('.defm-profile-preview, .defm-profile-edit').animate({opacity: 0}, 200, function() {
				$profileElement.addClass('edit');
				decomExport.$addEmailProfileForm.appendTo($profileElement).css({display: 'block'}).animate({opacity: 1}, 200);
				decomExport.$addEmailProfileForm.find('#defm_email_profile_name').focus();
			});
		});

		decomExport.$emailProfileList.on('click', '#defm_email_profile_submit.edit', function(event) {
			event.preventDefault();

			decomExport.$addEmailProfileForm.find('input, select').prop("disabled", true);
			$('#defm_email_profile_submit_wrapper').addClass('defm-working');
			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));

			decomExport.formData = {
				profile_id: profileID,
				profile_name: $('#defm_email_profile_name').val(),
				from: $('#defm_email_profile_from').val(),
				to: $('#defm_email_profile_to').val(),
				subject: $('#defm_email_profile_subject').val(),
				cc: $('#defm_email_profile_cc').val(),
				bcc: $('#defm_email_profile_bcc').val(),
				text: $('#defm_email_profile_text').val(),
				attachment: $('#defm_email_profile_attachment').is(':checked') ? 1 : 0,
			};
			var jqxhr = $.ajax(decomExport.ajaxUrl, {
				type: 'POST',
				data: {
					action: 'defm_add_email_profile',
					security: decomExport.security,
					form_data: decomExport.formData
				},
				success: function(response) {
					if (response.status === 'success') {
						$('#defm_schedule_email_profile').find('option[value="' + profileID + '"]').text(response.profileTitle);
						$profileElement.find('.defm-profile-title').text(response.profileTitle);
						$profileElement.find('.defm-profile-preview').remove();
						$profileElement.prepend($(response.element).find('.defm-profile-preview'));
						decomExport.appendElement('<div class="defm-status-line"><strong>Updated Email profile:</strong> ' + response.profileTitle + '</div>');
						decomExport.removeErrors();
						$('#defm_email_profile_submit_wrapper').removeClass('defm-working');
						decomExport.$addEmailProfileForm.find('input, select').prop("disabled", false);

						$profileElement.find('#defm_email_profile_cancel').click();

					} else {
						decomExport.displayNewEmailProfileError(response);
					}
				},
				error: decomExport.displayNewEmailProfileError
			});
		});

		decomExport.$emailProfileList.on('click', '#defm_email_profile_cancel.edit', function(event) {
			event.preventDefault();
			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));


			decomExport.$addEmailProfileForm.fadeOut(100, function() {
				$profileElement.removeClass('edit');
				$profileElement.find('.defm-profile-preview, .defm-profile-edit').animate({opacity: 1}, 200);
				decomExport.$emailProfileList.find('.defm-profile-buttons button').prop('disabled', false);
				$('#defm_email_profile_name').val(decomExport.defaultEmailProfileFormData.profile_name);
				$('#defm_email_profile_from').val(decomExport.defaultEmailProfileFormData.from);
				$('#defm_email_profile_to').val(decomExport.defaultEmailProfileFormData.to);
				$('#defm_email_profile_subject').val(decomExport.defaultEmailProfileFormData.subject);
				$('#defm_email_profile_cc').val(decomExport.defaultEmailProfileFormData.cc);
				$('#defm_email_profile_bcc').val(decomExport.defaultEmailProfileFormData.bcc);
				$('#defm_email_profile_text').val(decomExport.defaultEmailProfileFormData.text);
				$('#defm_email_profile_attachment').prop('checked', decomExport.defaultEmailProfileFormData.attachment);
				decomExport.$addEmailProfileForm.prependTo('#defm_email_profile_footer');
				decomExport.$addEmailProfileButton.prop('disabled', false);
			});
		});

		decomExport.$emailProfileList.on('click', '.defm-delete', function (event) {
			event.preventDefault();
			var profileID = parseInt($(this).data('decom-profile'));
			var $profileElement = $(document.getElementById('defm_profile_' + profileID));
			var profileTitle = $profileElement.find('.defm-profile-title').text();
			if (confirm('Delete ' + profileTitle + '?')) {
				$profileElement.find('.defm-profile-buttons').addClass('defm-working');
				$profileElement.find('.defm-profile-buttons button').prop('disabled', true);
				decomExport.profileData = {
					profile_id: profileID
				};
				var jqxhr = $.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_delete_profile',
						security: decomExport.security,
						form_data: decomExport.profileData
					},
					success: function (response) {
						if (response.status === 'success') {
							$profileElement.find('.defm-profile-buttons').removeClass('defm-working');
							$('#defm_schedule_email_profile').find('option[value="' + profileID + '"]').remove();
							decomExport.removeElement($profileElement);
							decomExport.removeErrors();
							decomExport.appendElement('<div class="defm-status-line"><strong>Deleted Email profile:</strong> ' + profileTitle + '</div>');
						} else {
							decomExport.displayNewEmailProfileError(response);
						}
					},
					error: decomExport.displayNewEmailProfileError
				});
			}
		});

		/**
		 *
		 * History tab
		 *
		 */

		decomExport.$historyList = $('#defm_history_reports');
		decomExport.$historyFilterType = $('#defm_filter_type');
		decomExport.$historyFilterGeneratedBy = $('#defm_filter_generated_by');
		decomExport.$historyDeleteButton = $('#defm_history_delete');

		decomExport.$historyList.on('click', '.defm-delete', function (event) {
			event.preventDefault();
			var reportID = parseInt($(this).data('decom-report'));
			var $reportElement = $(document.getElementById('defm_report_' + reportID));
			var reportTitle = $reportElement.find('.defm-report-title').text();
			if (confirm('Delete ' + reportTitle + '?')) {
				$reportElement.find('.defm-report-buttons').addClass('defm-working');
				$reportElement.find('.defm-report-buttons .button').prop('disabled', true);
				decomExport.reportData = {
					report_id: reportID
				};
				var jqxhr = $.ajax(decomExport.ajaxUrl, {
					type: 'POST',
					data: {
						action: 'defm_delete_report',
						security: decomExport.security,
						form_data: decomExport.reportData
					},
					success: function (response) {
						if (response.status === 'success') {
							$reportElement.find('.defm-report-buttons').removeClass('defm-working');
							decomExport.removeElement($reportElement);
							decomExport.removeErrors();
							decomExport.appendElement('<div class="defm-status-line"><strong>Deleted Report:</strong> ' + reportTitle + '</div>');
						} else {
							decomExport.displayNewEmailProfileError(response);
						}
					},
					error: decomExport.displayNewEmailProfileError
				});
			}
		});

		decomExport.filterHistoryReports = function(event) {
			var showClass = $(this).val();
			decomExport.$historyList.find('> li:not(' + showClass + ')').fadeOut(100).slideUp(400);
			var $showReports = decomExport.$historyList.find(showClass);
			$showReports.slideDown(100).fadeIn(400);
			if ($(this).attr('id') === 'defm_filter_type') {
				if (showClass === '.defm-auto-report') {
					$('#defm_generated_by_filter').show();
					decomExport.$historyFilterGeneratedBy.change();
				} else {
					$('#defm_generated_by_filter').hide();
				}

				if (showClass === '.defm-report') {
					decomExport.$historyDeleteButton.text('Delete All');
				} else {
					decomExport.$historyDeleteButton.text('Delete Filtered');
				}
			}
			decomExport.$historyDeleteButton.prop('disabled', !$showReports.length);
		};

		decomExport.$historyFilterType.on('change', decomExport.filterHistoryReports);
		decomExport.$historyFilterGeneratedBy.on('change', decomExport.filterHistoryReports);

		decomExport.$historyDeleteButton.on('click', function (event) {
			event.preventDefault();

			var $visibleReports = decomExport.$historyList.find('> li:visible:not(#defm_no_history_reports)');
			if ($visibleReports.length) {
				if (confirm('Are you sure you want to delete ' + $visibleReports.length + ' reports from history?')) {
					$visibleReports.each(function (index) {
						var reportID = parseInt($(this).data('decom-report'));
						var $reportElement = $(this);
						var reportTitle = $reportElement.find('.defm-report-title').text();
						$reportElement.find('.defm-report-buttons').addClass('defm-working');
						$reportElement.find('.defm-report-buttons button').prop('disabled', true);
						decomExport.reportData = {
							report_id: reportID
						};
						var jqxhr = $.ajax(decomExport.ajaxUrl, {
							type: 'POST',
							data: {
								action: 'defm_delete_report',
								security: decomExport.security,
								form_data: decomExport.reportData
							},
							success: function (response) {
								if (response.status === 'success') {
									$reportElement.removeClass('defm-working');
									decomExport.removeElement($reportElement);
									decomExport.removeErrors();
									decomExport.appendElement('<div class="defm-status-line"><strong>Deleted Report:</strong> ' + reportTitle + '</div>');
								} else {
									decomExport.displayNewEmailProfileError(response);
								}
							},
							error: decomExport.displayNewEmailProfileError
						});
					});
				}
			} else {
				alert('No reports to delete from history');
				$(this).prop('disabled', true);
			}
		});


	});

})( jQuery );
