<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'ANAC')
                <img src="{{ asset('assets/admin/imgs/logo.png') }}" class="logo" alt="ANAC">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
