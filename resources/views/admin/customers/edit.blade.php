@extends('adminlte::page')

@section('title', 'Edit Customer')

@section('content_header')
    <h1>Edit Customer</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile">Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $customer->mobile) }}" required>
                            @error('mobile')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $customer->dob ? $customer->dob->format('Y-m-d') : '') }}">
                            @error('dob')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="delivery_class">Delivery Class</label>
                            <select name="delivery_class" id="delivery_class" class="form-control @error('delivery_class') is-invalid @enderror">
                                <option value="">Select Delivery Class</option>
                                <option value="Inside Dhaka" {{ old('delivery_class', $customer->delivery_class) == 'Inside Dhaka' ? 'selected' : '' }}>Inside Dhaka</option>
                                <option value="Outside Dhaka" {{ old('delivery_class', $customer->delivery_class) == 'Outside Dhaka' ? 'selected' : '' }}>Outside Dhaka</option>
                            </select>
                            @error('delivery_class')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kam">KAM (Staff Responsible)</label>
                            <select name="kam" id="kam" class="form-control @error('kam') is-invalid @enderror">
                                <option value="">Select Staff Member</option>
                                @foreach($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}" {{ old('kam', $customer->kam) == $staffMember->id ? 'selected' : '' }}>{{ $staffMember->name }}</option>
                                @endforeach
                            </select>
                            @error('kam')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Additional JavaScript if needed
        });
    </script>
@stop
