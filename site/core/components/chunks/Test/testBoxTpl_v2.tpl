<div class="d-none" id="test-question-[[+MIGX_id]]">
    <div class="p-4">
        <p class="fs-5  pt-2 m-0 fw-medium">[[+question]]</p>
    
    </div>

    <div class="ps-2">
        <ul class="form-control-lg control-test">
            [[testBoxAnswer_v2?
                &val=`[[+answers]]`
                &id=`[[+MIGX_id]]`
            ]]
        </ul>
    </div>
    <div class="card m-4 mb-1 d-none" id="test-wrong-result-[[+MIGX_id]]">
        <div class="d-flex  flex-row g-0">
            <div class="">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bg-danger bi bi-x-lg p-2 rounded-start text-light" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"></path>
                    <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"></path>
                </svg>
            </div>
            <div class="align-items-center d-flex ps-3">
                <div class="">
                    <span>
                        Неправильный ответ!
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>