<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
    .input-group-append {
        cursor: pointer;
    }

    .gj-datepicker>button {
        border: none;
        background-color: rgb(16, 111, 199);
        color: white;
        
     
    }
    .bi-filetype-csv,.bi-file-earmark-spreadsheet,.bi-filetype-pdf,.bi-printer{
        font-size: 20px;
        color: rgb(22, 79, 165);
    }
    .gj-datepicker button:hover {
        background-color:  rgb(10, 84, 153);

    }
    .inputdate {
        /* display: flex;
        justify-content: center; */
    }
.dt-buttons>button{
    background-color:  transparent;

    border: none;
}
.dt-buttons>button:hover{
    background-color:  transparent;
    
}
</style>

<div class="inputdate row pt-4 pb-4">
    <div class="col-md-3">
        <label for="">From date</label>
        <input type="date" class="form-control date-range-filter" id="min" />
    </div>
    <div class="col-md-3">
        <label for="">To date</label>
        <input type="date" class="form-control date-range-filter" id="max" />
    </div>
    <div class="col-md-3">
        <label for="">Category</label>
        <select class="form-select" id="searchCategory" aria-label="Default select example">
            <option value="">All</option>
          </select>
    </div>
    <div class="col-md-3">
        <label for="">Product</label>
        <select class="form-select" id="searchProduct" aria-label="Default select example">
            <option value="">All</option>
          </select>
    </div>
</div>
