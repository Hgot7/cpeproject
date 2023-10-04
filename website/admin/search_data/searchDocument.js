// ========================= Search students =========================
$(document).ready(function() {
  let index = -1;
  let selectedItem;
  let selectedIndex;
  let lastSearchText = "";
  
  $(document).mousedown(function(e) {
    const container = $("#show-list");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      container.empty();
      index = -1;
    }
  });

  $("#search").focus(function() {               //เมื่อกลับมา focus ช่อง search 
    let searchText = $(this).val();
    if (searchText !== "") {
      $.ajax({
        url: "search_data/searchDocument.php",
        method: "post",
        data: {
          query: searchText
        },
        success: function(response) {
          $("#show-list").html(response);
          updateSelectedItem();
          filterResults(searchText);
          lastSearchText = searchText;
        }
      });
    } else {
      $("#show-list").empty();
      updateSelectedItem();
      index = -1;
      lastSearchText = "";
    }
});

  
  $("#search").keyup(function(event) {
    if (event.which !== 13) {
      let searchText = $(this).val();
      if (searchText != "") {
        if (searchText === lastSearchText) {
          filterResults(searchText);
        } else {
          $.ajax({
            url: "search_data/searchDocument.php",
            method: "post",
            data: {
              query: searchText
            },
            success: function(response) {
              $("#show-list").html(response);
              updateSelectedItem();
              filterResults(searchText);
              lastSearchText = searchText;
            }
          });
        }
      } else {
        $("#show-list").empty();
        updateSelectedItem();
        index = -1;
        lastSearchText = "";
      }
    }
  });
  $(document).keydown(function(event) {
    if ($("#show-list").children().length > 0) {
      if (event.which === 40 && selectedItem.text().toLowerCase() !== "no record.") { // Arrow Down
        event.preventDefault();
        if (index === -1) {
          index = 0;
        } else {
          index = (index + 1) % $("#show-list").children().length;
        }
        updateSelectedItem();
        scrollToSelectedItem();
      } else if (event.which === 38 && selectedItem.text().toLowerCase() !== "no record.") { // Arrow Up
        event.preventDefault();
        if (index === -1) {
          index = $("#show-list").children().length - 1;
        } else {
          index = (index - 1 + $("#show-list").children().length) % $("#show-list").children().length;
        }
        updateSelectedItem();
        scrollToSelectedItem();
      } else if (event.which === 13) { // Enter
        event.preventDefault();
        if (index !== -1) {
          selectedItem = $("#show-list").children().eq(index);
          $("#search").val(selectedItem.text());
          $("#show-list").empty();
          index = -1;
          // ส่ง form หรือเรียกใช้ฟังก์ชันสำหรับการส่งข้อมูลขึ้นไปยัง server ตามที่ต้องการทำ
          $('#submitSearch').submit();
        } else if ($("#show-list").children().length === 1) {
          // กรณีมีเพียงรายการเดียวใน show-list
          let listItem = $("#show-list").children().first();
          let listItemText = listItem.text();
          let searchText = $("#search").val();
          if (searchText.toLowerCase() === listItemText.toLowerCase()) {
            $("#search").val(listItemText);
            $("#show-list").empty();
            index = -1;
            // ส่ง form หรือเรียกใช้ฟังก์ชันสำหรับการส่งข้อมูลขึ้นไปยัง server ตามที่ต้องการทำ
            $('#submitSearch').submit();
          }
        }
      }
    }
  });
  $(document).on('click', 'a', function() {
    if ($(this).closest('#show-list').length) {  //ใช้ .closest('#show-list').length เพื่อตรวจสอบว่าองค์ประกอบของลิงก์ที่ถูกคลิกอยู่ใน show-list หรือไม่
      $("#search").val($(this).text());
      $("#search").focus();
      $("#show-list").empty();
      index = -1;// เมื่อเลือกรายการแล้ว รีเซ็ตตำแหน่งเลือกเป็น -1
      // เรียกใช้ฟังก์ชัน updateSelectedItem เพื่อล้างคลาส "active" เมื่อคลิกที่รายการ
      updateSelectedItem();
    }
  });
  
  function updateSelectedItem() {
    const items = $("#show-list").children();
    selectedItem = items.eq(index);
    items.removeClass("active");
    if (index !== -1) {
      selectedItem.addClass("active");
    }
    selectedIndex = index;
  }
  function scrollToSelectedItem() {
    const container = $("#show-list");
    const containerHeight = container.height();
    const itemHeight = selectedItem.outerHeight();
    const itemTop = selectedItem.position().top;
    const scrollTop = container.scrollTop();
    if (itemTop < 0) {
      container.scrollTop(scrollTop + itemTop);
    } else if (itemTop + itemHeight > containerHeight) {
      container.scrollTop(scrollTop + itemTop + itemHeight - containerHeight);
    }
  }
  function filterResults(searchText) {
    let resultList = $("#show-list").children().filter(function() {
      return $(this).text().toLowerCase() === searchText.toLowerCase();
    });
    if (resultList.length > 1) {
      resultList = resultList.first();
    }
    if (resultList.length === 1) {
      $("#search").val(resultList.text());
      $("#show-list").empty();
      index = -1;
      // ส่ง form หรือเรียกใช้ฟังก์ชันสำหรับการส่งข้อมูลขึ้นไปยัง server ตามที่ต้องการทำ
      $('#submitSearch').submit();
    } else {
      updateSelectedItem();
    }
  }
});
// ========================= Search students =========================