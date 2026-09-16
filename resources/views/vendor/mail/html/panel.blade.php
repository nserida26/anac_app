<table class="panel" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td style="padding: 24px 0;">
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                <tr>
                    <td style="padding: 20px 24px;">
                        {{ Illuminate\Mail\Markdown::parse($slot) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
