@extends('admin.layouts.master')
@section('title', 'View Faq')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="admin-help-pending-content-box f8-bg rounded-20  border-e7">
        <div class="row gy-lg-0 gy-4">
            <div class="col-12">
                <h4 class="f-20 bold title-main">View  Faq</h4>
                <div class="card">
                    <div class="card-body">
                        
                        <form id="edit-subscription" method="post" action="{{route('update-faq', $faq_detail->id)}}">
                            @csrf
                            <div class="form-group">
                                <label for="question">Question</label>
                                <textarea id="question"  name="question" class="form-control text-black rounded-10  @error('question') is-invalid @enderror"  style="height: 100px"> {{$faq_detail->question}}</textarea>       
                                @error('question')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                           
                            <div class="form-group">
                                <label for="answer">Answer</label>
                                <textarea id="answer" name="answer" class="form-control text-black rounded-10  @error('answer') is-invalid @enderror"  style="height: 100px"> {{$faq_detail->answer}}</textarea>       
                                @error('answer')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mr-2" id="submitButton">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        $("textarea#question").val($.trim($("textarea#question").val()));
        $("textarea#answer").val($.trim($("textarea#answer").val()));
    });
    
    $(document).ready(function () {
        $("#edit-subscription").validate({
            rules: {
                question: {
                    required: true, 
                },
                answer: {
                    required: true, 
                }
            },
            messages: {
                question: {
                    required: "Question is required.",
                },
                answer: {
                    required: "Answer is required.",
                }
            },
            errorPlacement: function (error, element) {
                if (element.prop("tagName").toLowerCase() === "textarea") {
                    error.insertAfter(element); // Ensures error is displayed right after the textarea
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>


@endsection