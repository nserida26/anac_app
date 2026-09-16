<table class="email-subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e9ecef;">
    <tr>
        <td>
            {{ Illuminate\Mail\Markdown::parse($slot) }}
        </td>
    </tr>
</table>
