@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Referrals graph</h1>

    <div id="referral-graph" style="height: 600px; border: 1px solid #ddd;"></div>

    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <script>
        fetch("{{ route('admin.referrals.graph') }}")
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('referral-graph');
                const nodes = new vis.DataSet(data.nodes);
                const edges = new vis.DataSet(data.edges);
                new vis.Network(container, { nodes, edges }, {
                    layout: { improvedLayout: true },
                    physics: { stabilization: true }
                });
            });
    </script>
@endsection
