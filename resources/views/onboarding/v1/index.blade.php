<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Getlead CRM | User Onboarding</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ url('onboarding/v1/css/custom.css') }}">
    <link rel="stylesheet" href="{{ url('onboarding/v1/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ url('onboarding/v1/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
</head>

<body>
    
    <div class="wizard-overlay" id="wizardOverlay">
        <div class="wizard-popup">
            <div class="wizard-step active" id="step1">
                <div class="slide-counter">1 of 4</div>
                <div class="box">
                    <img src="{{ url('onboarding/v1/img/1.svg') }}" class="img-fluid" height="320">
                </div>
                <div class="box">
                    <h2>Hi {{ ucfirst($user->vchr_user_name) }}, what industry are you in?</h2>
                    <p>Understanding your industry will help us tailor our CRM features to better suit your specific needs. By knowing the field you're involved in, we can offer more relevant tools and support. This information is crucial for us to ensure that you get the most out of our CRM system. We aim to provide you with the best possible experience.</p>
                    <select id="industrySelect">
                        <option value="Travel and Tourism">Travel and Tourism</option>
                        <option value="IT">IT</option>
                        <option value="Jewellers">Jewellers</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="E-commerce">E-commerce</option>
                        <option value="Education">Education</option>
                        <option value="Retail">Retail</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Hospitality">Hospitality</option>
                        <option value="Telecommunications">Telecommunications</option>
                        <option value="Real Estate">Real Estate</option>
                        <option value="Financial Services">Financial Services</option>
                        <option value="Consulting">Consulting</option>
                        <option value="Automotive">Automotive</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="wizard-step" id="step2">
                <div class="slide-counter">2 of 4</div>
                <div class="box"><img src="{{ url('onboarding/v1/img/2.svg') }}" class="img-fluid"  height="320"></div>
                <div class="box">
                    
                    <h2>How many staff members are there at your company?</h2>
                    <p>This information will help us customize our support and services to fit your organization's size. By knowing these details, we can provide you with the most relevant tools and assistance.</p>
                    <div class="team-size-buttons">
                        <button class="team-size-btn" data-value="onlyme">Only Me</button>
                        <button class="team-size-btn" data-value="2-5">2-5</button>
                        <button class="team-size-btn" data-value="6-10">6-10</button>
                        <button class="team-size-btn" data-value="10-15">10-15</button>
                        <button class="team-size-btn" data-value="16-20">16-20</button>
                        <button class="team-size-btn" data-value="21-30">21-30</button>
                        <button class="team-size-btn" data-value="31-40">31-40</button>
                        <button class="team-size-btn" data-value="41-50">41-50</button>
                        <button class="team-size-btn" data-value="50-100">50-100</button>
                        <button class="team-size-btn" data-value="100 Above">100 Above</button>
                    </div>
                    
                </div>
            </div>
            <div class="wizard-step" id="step3">
                <div class="slide-counter">3 of 4</div>
                <div class="box"><img src="{{ url('onboarding/v1/img/3.svg') }}" class="img-fluid" height="320"></div>
                <div class="box">
                    <h2>Pick the source of your leads.</h2>
                    <p>Identifying the origin of your leads helps us understand which channels are most effective for your business. Please select the primary source from where your leads are generated, such as referrals, social media, email campaigns, website inquiries, or other channels.</p>
                    <div class="tag-container">
                        <input type="text" class="input-tag mb-0" placeholder="Add a tag">
                    </div>
                    <p class="pb-0">Suggested Tags click to add:</p>
                    <div class="suggested-tags">
                        <div class="suggested-tag">Facebook</div>
                        <div class="suggested-tag">Instagram</div>
                        <div class="suggested-tag">WhatsApp</div>
                        <div class="suggested-tag">Advertisement</div>
                        <div class="suggested-tag">Website</div>
                        <div class="suggested-tag">Referral</div>
                        <div class="suggested-tag">Networking Event</div>
                        <div class="suggested-tag">Partner</div>
                        <div class="suggested-tag">IVR</div>
                    </div>
                    
                </div>
            </div>
            <div class="wizard-step" id="step4">
                <div class="slide-counter">4 of 4</div>
                <div class="box"><img src="{{ url('onboarding/v1/img/4.svg') }}" class="img-fluid" height="320"></div>
                <div class="box">
                    <h2>Map your customer journey</h2>
                    <p>Understanding your customer journey is crucial for optimizing your sales and marketing strategies. Please outline the typical steps your customers take from initial contact to final purchase, including any key touchpoints and interactions along the way.</p>
                    
                    <div id="statuses">
                        <div class="status-box">New <i class="fas fa-times remove-status"></i></div>
                        <div class="status-box">Connected <i class="fas fa-times remove-status"></i></div>
                        <div class="status-box">Qualified <i class="fas fa-times remove-status"></i></div>
                        <div class="add-status-box" id="add-next-status">
                            <i class="fas fa-plus"></i> Add next status
                        </div>
                    </div>
                    
                    <div class="lead-sources-columns">
                        <div class="lead-sources-column">
                            <h5 class="text-center">Success</h5>
                            <div class="line"></div>
                            
                            <div id="success-status">
                                <div class="status-box success">Closed <i class="fas fa-times remove-status"></i></div>
                                <div class="add-status-box" id="add-success-status">
                                    <i class="fas fa-plus"></i> Add status
                                </div>
                            </div>
                            
                        </div>
                        <div class="lead-sources-column">
                            <h5 class="text-center">Failed</h5>
                            <div class="line"></div>
                            
                            <div id="failed-status">
                                <div class="status-box failed">Lost <i class="fas fa-times remove-status"></i></div>
                                <div class="add-status-box" id="add-failed-status">
                                    <i class="fas fa-plus"></i> Add status
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Add Status Modal -->
                    <div class="modal fade" id="addStatusModal" tabindex="-1" aria-labelledby="addStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <label for="statusName">Status name:</label>
                                    <input type="text" id="statusName" name="statusName" class="form-control">
                                </div>
                                <div class="modal-footer border-none pt-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cls">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveStatus">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add Failed Status Modal -->
                    <div class="modal fade" id="addFailedStatusModal" tabindex="-1" aria-labelledby="addFailedStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <label for="failedStatusName">Failed status name:</label>
                                    <input type="text" id="failedStatusName" name="failedStatusName" class="form-control">
                                </div>
                                <div class="modal-footer border-none pt-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cls">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveFailedStatus">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="addSuccessStatusModal" tabindex="-1" aria-labelledby="addSuccessStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <label for="failedStatusName">Success status name:</label>
                                    <input type="text" id="successStatusName" name="successStatusName" class="form-control">
                                </div>
                                <div class="modal-footer border-none pt-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cls">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveSuccessStatus">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wizard-nav">
                <button id="prevBtn">Back</button>
                <button id="nextBtn">Continue <i class="fas fa-spinner fa-spin loader"></i> </button>
            </div>
        </div>
    </div>
    
    <script src="{{ url('onboarding/v1/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('onboarding/v1/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('onboarding/v1/js/jquery-ui.js') }}"></script>
    <script>
        $(document).ready(function () {
            let currentStep = 1;
            var addedTags = []; // Array to store added tags
            var selectedTeamSize = '';
            var base_url = @json(url('/'));
            
            $('#wizardOverlay').fadeIn();
            showStep(currentStep);
            
            function showStep(step) {
                if(currentStep == 1)
                    $('#prevBtn').css('opacity',.3);
                else
                    $('#prevBtn').css('opacity',1);

                $('.wizard-step').removeClass('active');
                $('#step' + step).addClass('active');
            }
            
            $('#prevBtn').click(function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            
            $('#nextBtn').click(function () {
                industry = $('#industrySelect').val();
                var statuses = getStatuses('#statuses');
                var successStatuses = getStatuses('#success-status');
                var failedStatuses = getStatuses('#failed-status');
                var allStatuses = statuses.concat(successStatuses, failedStatuses);
                if (currentStep < 4) {
                    currentStep++;
                    showStep(currentStep);
                }else{
                    $(this).addClass('btn-disabled');
                    $(this).find('.loader').css('display', 'inline-block');
                    saveOnboardingData(industry,selectedTeamSize,addedTags,allStatuses);
                }
                console.log(currentStep)
            });
            
            showStep(currentStep);
            
            function addTag(tag) {
                if (tag && $('.tag').length < 10 && !addedTags.includes(tag)) {
                    var tagElement = $('<div class="tag"></div>').text(tag);
                    var removeIcon = $('<i class="fas fa-times"></i>').click(function() {
                        $(this).parent().remove();
                        addedTags = addedTags.filter(function(value) {
                            return value !== tag;
                        });
                    });
                    tagElement.append(removeIcon);
                    $('.input-tag').before(tagElement);
                    addedTags.push(tag);
                }
            }
            
            $('.input-tag').on('keypress', function(e) {
                if (e.which === 13) { // Enter key pressed
                    e.preventDefault();
                    addTag($(this).val());
                    $(this).val('');
                }
            });
            
            $('.suggested-tag').click(function() {
                addTag($(this).text());
            });
            
            $('.team-size-btn').click(function () {
                $('.team-size-btn').removeClass('active');
                $(this).addClass('active');
                selectedTeamSize = $(this).data('value');
                // You can perform further actions here, such as submitting the form or updating UI
            });
            
            $("#statuses, #success-status, #failed-status").sortable({
                items: ".status-box",
                placeholder: "ui-state-highlight",
                stop: function (event, ui) {
                    // Handle the stop event if needed
                }
            }).disableSelection();
            
            $('#add-next-status').on('click', function () {
                $('#addStatusModal').modal('show');
            });
            
            $('#add-failed-status').on('click', function () {
                $('#addFailedStatusModal').modal('show');
            });
            $('#add-success-status').on('click', function () {
                $('#addSuccessStatusModal').modal('show');
            });
            
            $('#saveStatus').on('click', function () {
                let newStatus = $('#statusName').val();
                if (newStatus) {
                    $('#add-next-status').before('<div class="status-box">' + newStatus + ' <i class="fas fa-times remove-status"></i></div>');
                    $('#addStatusModal').modal('hide');
                    $('#statusName').val('');
                }
            });
            
            $('#saveFailedStatus').on('click', function () {
                let newFailedStatus = $('#failedStatusName').val();
                if (newFailedStatus) {
                    $('#add-failed-status').before('<div class="status-box failed">' + newFailedStatus + ' <i class="fas fa-times remove-status"></i></div>');
                    $('#addFailedStatusModal').modal('hide');
                    $('#failedStatusName').val('');
                }
            });
            
            $('#saveSuccessStatus').on('click', function () {
                let newFailedStatus = $('#successStatusName').val();
                if (newFailedStatus) {
                    $('#add-success-status').before('<div class="status-box success">' + newFailedStatus + ' <i class="fas fa-times remove-status"></i></div>');
                    $('#addSuccessStatusModal').modal('hide');
                    $('#cussessStatusName').val('');
                }
            });
            
            $(document).on('click', '.remove-status', function () {
                $(this).parent().remove();
            });
            
            function getStatuses(selector) {
                var statuses = [];
                $(selector).find('.status-box').each(function() {
                    var statusText = $(this).clone().children().remove().end().text().trim();
                    statuses.push(statusText);
                });
                return statuses;
            }
            
            function saveOnboardingData(industry,teams,sources,statues) {
                $.ajax({
                    type: "POST",
                    url: base_url+'/setup/save-onboarding-data',
                    data: {
                        "industry":industry,
                        "teams": teams,
                        "sources": sources,
                        "statues": statues
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.status === 1) {
                            $('#wizardOverlay').fadeOut();
                            location.href = @json(route('home'))
                        }else{
                            $('#nextBtn').removeClass('btn-disabled');
                            $('#nextBtn').find('.loader').hide();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>