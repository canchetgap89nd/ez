import { Bar } from 'vue-chartjs'

export default {
  extends: Bar,
  mounted () {
    // Overwriting base render method with actual data.
    this.renderChart({
      labels: ["Mới", "Chờ giao hàng", "Hàng đang giao", "Đã giao hàng", "Khách trả lại", "Đã nhận lại hàng", "Đơn bị hủy", "Thành công"],
      datasets: [{
          label: '# of Votes',
          data: [12, 19, 3, 5, 2, 3, 10, 11, 20],
          backgroundColor: [
              '#00b140',
              '#00b140',
              '#00b140',
              '#00b140',
              '#00b140',
              '#00b140',
              '#00b140',
              '#00b140',
              '#00b140'
          ],
      }]
    })
  }
}