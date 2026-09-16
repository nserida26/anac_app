<tr>
    <td class="header" style="padding: 0;">
        <a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
            @if (trim($slot) === 'ANAC')
                <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC" width="80" style="height: auto;">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
