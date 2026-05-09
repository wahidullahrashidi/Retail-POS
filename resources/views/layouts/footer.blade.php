<footer id="app-footer">
    <div class="footer-left">
        <div class="footer-item">
            <span class="status-dot"></span>
            <span>{{ __('messages.system_online') }}</span>
        </div>
        <div class="footer-item">
            <i class="fas fa-rotate" style="font-size:10px"></i>
            <span>{{ __('messages.last_sync') }} <span id="lastSyncTime">-</span></span>
        </div>
    </div>
    <div class="footer-right">
        <div class="footer-item" style="font-family:'DM Mono',monospace" id="footerClock">--:--:--</div>
    </div>
</footer>
