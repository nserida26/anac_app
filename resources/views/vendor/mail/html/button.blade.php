<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center" style="padding: 16px 0 24px 0;">
            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td>
                        <a href="{{ $url }}" class="email-btn email-btn--{{ $color ?? 'primary' }}"
                           target="_blank" rel="noopener"
                           style="display: inline-block;
                                  background: {{ ($color ?? 'primary') === 'accent' ? '#c8a951' : '#0d2137' }};
                                  color: #ffffff;
                                  text-decoration: none;
                                  padding: 14px 36px;
                                  border-radius: 8px;
                                  font-size: 15px;
                                  font-weight: 600;
                                  letter-spacing: 0.3px;
                                  font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                            {{ $slot }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
