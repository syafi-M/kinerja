<x-app-layout>
    <x-main-div>
        <form action="{{ route('export_checklist')}}" method="POST">
        @csrf
        	@forelse ($arr as $i)
                <input type="checkbox" name="check[]" value="{{ $i->id}}"/>
            @empty
                <p>Kosong</p>
            @endforelse
            <button type="submit">Save</button>
        </form>
    </x-main-div>
</x-app-layout>