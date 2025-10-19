<div class="modal" id="subscriptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalPlanName">Plan Name</h3>
            <div class="modal-plan-price">$<span id="modalPlanPrice">0.00</span></div>
        </div>
        <div class="modal-features">
            <div class="modal-feature"><b>Books:</b> <span id="modalBookQuantity">0</span></div>
            <div class="modal-feature"><b>Audio Books:</b> <span id="modalAudioBookQuantity">0</span></div>
            <div class="modal-feature"><b>Validity:</b> <span id="modalValidityDays">0</span> Days</div>
        </div>
        <div class="payment-method">
            <h4>Select Payment Method</h4>
            <div style="display:flex;gap:1rem;margin-bottom:1rem;">
                <button class="payment-option" data-method="bkash">
                    <i class="fas fa-mobile-alt" style="color:#e2136e;"></i> bKash
                </button>
                <button class="payment-option" data-method="card">
                    <i class="far fa-credit-card" style="color:#0061a8;"></i> Card
                </button>
            </div>
            <div id="paymentDetails" style="display:none;"></div>
        </div>
        <div class="modal-actions" style="margin-top:1rem;text-align:right;">
            <button class="modal-btn modal-btn-close" onclick="closeModal()">Close</button>
            <button class="modal-btn modal-btn-confirm" id="confirmSubscription" disabled>Confirm Subscription</button>
        </div>
    </div>
</div>
